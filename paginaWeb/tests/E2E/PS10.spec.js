import { test, expect } from '@playwright/test';

test.describe('Ciclo de vida del dato PS_10', () => {
    // Definimos el producto que sabemos que debe expirar
    const productoExpirado = 'Jitomate';
    const establecimiento = 'Tienda prueba PS_10';

    test('El registro caducado debe desaparecer del mapa y la lista tras la limpieza', async ({ page }) => {

        // Se asume que el registro ya ha sido borrado en el backend

        // Va a la búsqueda e ingresa el nombre del producto expirado
        await page.goto('/');
        const inputBusqueda = page.locator('input[name="query"]').first();
        await inputBusqueda.fill(productoExpirado);

        // Prepara la escucha de la API para verificar qué devuelve el servidor
        const respuestaPromesa = page.waitForResponse(response =>
            response.url().includes('/api/buscar') && response.status() === 200
        );
        // Hace clic en buscar y espera la respuesta
        await page.click('.btn_buscar_inicio');
        const respuesta = await respuestaPromesa;
        const datosRecibidos = await respuesta.json();

        // Si la API devuelve el arreglo
        const productosArray = Array.isArray(datosRecibidos)
            ? datosRecibidos
            : (datosRecibidos.productos || []); // Ajusta productos según el JSON

        // El producto expirado no debería estar en el JSON de respuesta
        const existeEnJson = productosArray.some(p => p.tienda === establecimiento);
        expect(existeEnJson).toBe(false);

        // El marcador en el mapa no debe existir
        const marcador = page.locator(`.leaflet-marker-icon[title*="${establecimiento}"]`);
        await expect(marcador).not.toBeVisible();

        // La lista de resultados no debe contener la tarjeta de ese establecimiento
        const tarjetaProducto = page.locator('#contenedor_tarjetas').getByText(establecimiento);
        await expect(tarjetaProducto).not.toBeVisible();

        // Si no hay más productos con ese nombre, debería aparecer el SweetAlert de "Sin resultados"
        const alerta = page.locator('.swal2-popup');
        if (datosRecibidos.length === 0) {
            await expect(alerta).toBeVisible();
            await expect(alerta).toContainText('No encontramos productos');
        }
    });
});