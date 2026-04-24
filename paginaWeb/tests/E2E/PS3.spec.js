import { test, expect } from '@playwright/test';

test.describe('PS_3: Sistema de Reputación (Likes/Dislikes)', () => {
    
    test('Validar aprobación de precio y persistencia post-recarga', async ({ page }) => {
        // Navegar al inicio
        await page.goto('/');

        const buscador = page.getByPlaceholder('Jitomate, cebolla...');
        await buscador.fill('Jitomate'); 
        await page.getByRole('button', { name: 'BUSCAR' }).click();

        // Esperamos a que los resultados carguen (el robot esperará al primer botón de votar)
        const botonLike = page.locator('button.btn_votar[data-voto="true"]').first();
        const contadorSpan = botonLike.locator('.conteo_votos');
        const textoInicial = await contadorSpan.innerText();
        const valorInicial = parseInt(textoInicial);

        // Ejecutar el voto
        const startTime = Date.now();
        await botonLike.click();

        // Validar feedback inmediato
        await expect(contadorSpan).toHaveText((valorInicial + 1).toString());
        const duration = Date.now() - startTime;
        expect(duration).toBeLessThan(3000); 

        // Validar persistencia (REQ-1.10F)
        await page.reload();
        if (page.url() === 'http://localhost:3000/') {
            await buscador.fill('Jitomate');
            await page.getByRole('button', { name: 'BUSCAR' }).click();
        }

        await expect(page.locator('button.btn_votar[data-voto="true"]').first().locator('.conteo_votos'))
            .toHaveText((valorInicial + 1).toString());
    });
});