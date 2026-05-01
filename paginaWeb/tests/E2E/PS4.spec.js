import { test, expect } from '@playwright/test';

test.describe('Búsqueda y criterio de ordenamiento PS_4', () => {

    test('Validar filtros de precio y distancia con tiempos de respuesta', async ({ page }) => {
        // Va a la página principal y realiza una búsqueda para cargar resultados
        await page.goto('/');
        const inputBusqueda = page.locator('.bloque_central input[name="query"]');
        await inputBusqueda.fill('Leche');

        // Espera la respuesta inicial de la API
        const respuestaBusqueda = page.waitForResponse(response => 
            response.url().includes('/api/buscar') && response.status() === 200
        );
        // Hace clic en el botón de búsqueda
        await page.click('.btn_buscar_inicio');
        await respuestaBusqueda;
        // Verificar que el contenedor de tarjetas esté visible
        const contenedorTarjetas = page.locator('#contenedor_tarjetas');
        await expect(contenedorTarjetas).toBeVisible();

        // Ordena por precio y mide el tiempo de respuesta
        const startPrecio = Date.now();
        
        // Es un ordenamiento frontend, entonces valida el cambio en el DOM 
        await page.selectOption('select#orden', 'precio'); 

        // Localiza los precios de las tarjetas
        const precios = page.locator('.precio_destacado');
        await expect(precios.first()).toBeVisible();
        
        // Valida rendimiento
        const durationPrecio = (Date.now() - startPrecio) / 1000;
        console.log(`Ordenamiento por precio completado en: ${durationPrecio}s`);
        expect(durationPrecio).toBeLessThan(5);

        // Extrae y comparar precios
        const listaPreciosStr = await precios.allInnerTexts();
        // Convierte a números, eliminando símbolos
        const numPrecios = listaPreciosStr.map(p => parseFloat(p.replace(/[^0-9.]/g, ''))); 
        // Verifica que los precios estén ordenados de menor a mayor
        if (numPrecios.length > 1) {
            expect(numPrecios[0]).toBeLessThanOrEqual(numPrecios[1]);
        }

        // Ordena por distancia y mide el tiempo de respuesta
        const startDistancia = Date.now();
        await page.selectOption('select#orden', 'distancia');

        // Valida la fluidez del cambio
        const durationDistancia = (Date.now() - startDistancia) / 1000;
        console.log(`Ordenamiento por distancia completado en: ${durationDistancia}s`);
        expect(durationDistancia).toBeLessThan(5);
    });
});