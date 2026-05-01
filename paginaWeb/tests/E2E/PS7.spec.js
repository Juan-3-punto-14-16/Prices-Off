import { test, expect } from '@playwright/test';

test.describe('Rendimiento del scroll PS_7', () => {
    test('Validar fluidez del scroll con carga de datos', async ({ page }) => {
        await page.goto('/');

        // Simula búsqueda para llenar la lista
        const inputBusqueda = page.locator('input[name="query"]').first();
        await inputBusqueda.fill('Limon');
        await page.click('.btn_buscar_inicio');

        // Espera a que las tarjetas se rendericen
        const listaCards = page.locator('#contenedor_tarjetas');
        await expect(listaCards).toBeVisible();

        // Ejecuta scroll y mide el rendimiento
        const metrics = await page.evaluate(async () => {
            const contenedor = document.querySelector('#contenedor_tarjetas'); 
            // Si el contenedor no existe, retornamos un resultado que indique fallo
            if (!contenedor) return { movimientoReal: -1 };

            // Guarda la posición inicial del scroll
            const posicionInicial = contenedor.scrollTop;
            let scrollCount = 0;
            const start = performance.now();

            // Realiza scroll hacia abajo varias veces para simular la interacción del usuario
            return new Promise((resolve) => {
                const interval = setInterval(() => {
                    // Forza el scroll hacia abajo
                    contenedor.scrollTop += 150; 
                    scrollCount++;
                    
                    // Finaliza tras 10 iteraciones
                    if (scrollCount > 10) { 
                        clearInterval(interval);
                        const end = performance.now();
                        resolve({
                            duration: end - start, // Tiempo total del scroll
                            // Si sigue dando 0, es probable que el CSS impida el scroll
                            movimientoReal: contenedor.scrollTop - posicionInicial, 
                            posicionFinal: contenedor.scrollTop
                        });
                    }
                }, 100);
            });
        });

        console.log(`Píxeles desplazados: ${metrics.movimientoReal}px`);
        console.log(`Tiempo de ejecución: ${metrics.duration.toFixed(2)}ms`);
        
        // Debe haber desplazamiento real y tiempo de ejecución razonable
        expect(metrics.movimientoReal).toBeGreaterThan(0);
        expect(metrics.duration).toBeLessThan(3000);
    });
});