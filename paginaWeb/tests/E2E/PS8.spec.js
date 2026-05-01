import { test, expect } from '@playwright/test';

test.describe('Retroalimentación visual PS_8', () => {

    test('Validar mensaje de "Sin resultados" y estado del mapa', async ({ page }) => {
        // Se posiciona en la barra de búsqueda de la página principal
        await page.goto('/');
        const inputBusqueda = page.locator('input[name="query"]').first();

        // Escribe el nombre de un producto irreal o inexistente
        await inputBusqueda.fill('Carne de dinosaurio');

        // Prepara el listener para la respuesta de la API
        const respuestaBusqueda = page.waitForResponse(response => 
            response.url().includes('/api/buscar') && response.status() === 200
        );

        // Presiona el botón de buscar
        await page.click('.btn_buscar_inicio');
        
        // Espera a que la API confirme que no hay datos
        await respuestaBusqueda;

        // Verificación de la alerta SweetAlert usas Swal.fire con el título "Sin resultados"
        const alerta = page.locator('.swal2-popup');
        await expect(alerta).toBeVisible();
        await expect(alerta).toContainText('No encontramos productos con ese nombre');

        // Verificación del mapa limpio, sin marcadores de productos
        const marcadores = page.locator('.leaflet-marker-icon');
        
        // El mapa no debería tener marcadores
        const conteoMarcadores = await marcadores.count();
        expect(conteoMarcadores).toBeLessThan(1);

        // Al no haber productos, el contenedor de tarjetas debería estar vacío
        const contenedorTarjetas = page.locator('#contenedor_tarjetas');
        await expect(contenedorTarjetas).toBeEmpty();
    });
});