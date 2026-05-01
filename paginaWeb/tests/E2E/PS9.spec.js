import { test, expect } from '@playwright/test';

test.describe('Prevención de errores en formularios PS_9', () => {

    test('Validar interceptación de errores antes del envío al servidor', async ({ page }) => {
        // Abrir el formulario de registro de productos
        await page.goto('/agregar'); 

        // Input de nombre y botón de envío
        const inputNombre = page.locator('[name="nombre[]"]').first();
        const btnEnviar = page.locator('#formulario_productos button[type="submit"]');

        // Intentar enviar el formulario sin escribir nada
        await btnEnviar.click();
        // Verificar que el campo de nombre es inválido
        const isValid = await inputNombre.evaluate((node) => node.validity.valid);
        expect(isValid).toBe(false);

        // Verificar mensaje del navegador
        const validationMessage = await inputNombre.evaluate((node) => node.validationMessage);
        expect(validationMessage).not.toBe('');

        // Verificar que NO se hizo la llamada a la API
        page.on('request', request => {
            if (request.url().includes('/api/guardar')) {
                throw new Error('Se intentó llamar a la API a pesar de tener campos vacíos');
            }
        });
    });
});