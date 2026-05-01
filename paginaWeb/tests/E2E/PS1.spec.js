import { test, expect } from '@playwright/test';

test.use({
    permissions: ['geolocation'],
    geolocation: { latitude: 20.6668, longitude: -103.3508 }, // Coordenadas de ejemplo
});

test.describe('Registro de productos manual y navegación al mapa PS_1', () => {
    test('Debe llenar el formulario y validar el registro', async ({ page }) => {
        // Ir al formulario para agregar producto
        await page.goto('/agregar');

        // Esperar a que la geolocalización se establezca
        await page.waitForFunction(() => {
            return typeof latitud !== 'undefined' && latitud !== 20.655262;
        }, { timeout: 5000 }).catch(() => console.log("Usando ubicación por defecto"));

        // Llenar con payload de prueba
        const productoNombre = 'Coca Cola';
        const tiendaNombre = 'Tienda PS_1';
        const precio = '18.00';

        await page.locator('input[name="nombre[]"]').first().fill(productoNombre);
        await page.locator('input[name="precio[]"]').first().fill(precio);
        await page.locator('input[name="cantidad[]"]').first().fill('1');
        await page.locator('select[name="unidad[]"]').first().selectOption('pieza');
        await page.locator('input[name="establecimiento"]').fill(tiendaNombre);

        // Antes de enviar, verificar si el input de dirección ya tiene texto
        const direccionInput = page.locator('#direccion_input');
        await expect(direccionInput).not.toHaveValue('', { timeout: 5000 }).catch(() => { });

        // Botón para enviar el formulario
        await page.getByRole('button', { name: /ENVIAR/i }).click();
        // Verificar que aparezca la alerta de SweetAlert
        const alerta = page.locator('.swal2-container');
        await expect(alerta).toBeVisible();
        await page.click('.swal2-confirm');

        // Volver al mapa y buscar
        await page.goto('/');
        const buscador = page.locator('.bloque_central input[name="query"]');
        await buscador.fill(productoNombre);
        await page.keyboard.press('Enter');

        // Esperamos a que la API responda y el marcador aparezca
        const marcador = page.locator('.leaflet-marker-icon').last();
        await marcador.waitFor({ state: 'visible' });

        // Forzamos el click porque a veces Leaflet encima elementos transparentes
        await marcador.click({ force: true });

        const popup = page.locator('.leaflet-popup-content');
        await expect(popup).toBeVisible({ timeout: 8000 });

        // Imprime el contenido del popup
        const textoReal = await popup.innerText();
        console.log('Contenido detectado:', textoReal);

        // Valida lo que el log dice
        await expect(popup).toContainText(precio);
        await expect(popup).toContainText(tiendaNombre);
    });
});

