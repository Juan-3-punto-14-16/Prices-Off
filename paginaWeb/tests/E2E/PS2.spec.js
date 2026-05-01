import { test, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

// Obtener la ruta del directorio actual para construir la ruta de la imagen
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

test.describe('Realiza escaneo de Ticket y verifica rendimiento PS_2', () => {

    test('Validar escaneo de ticket y tiempo de procesamiento', async ({ page }) => {
        // Navegar a la página de registro
        await page.goto('/agregar');

        // Preparar la captura del selector de archivos antes de hacer clic
        const [fileChooser] = await Promise.all([
            page.waitForEvent('filechooser'),
            page.click('#btn_escanear')
        ]);

        // Construir la ruta subiendo un nivel desde E2E hacia archivos_prueba
        const rutaImagen = path.join(__dirname, '..', 'archivos_prueba', 'imagen_valida.jpeg');
        
        // Cargar la imagen simulando la cámara
        await fileChooser.setFiles(rutaImagen);

        //Iniciar cronómetro para validar que no pase de 30 segundos
        const startTime = Date.now();

        // Esperar a que el primer campo de nombre se llene
        const inputNombre = page.locator('input[name="nombre[]"]').first();
        
        // Aplicar el timeout de 30s
        await expect(inputNombre).not.toBeEmpty({ timeout: 30000 });

        const duration = (Date.now() - startTime) / 1000;
        console.log(`Procesado en: ${duration}s`);

        // Validar que el tiempo fue menor al límite
        expect(duration).toBeLessThan(30);
    });
});