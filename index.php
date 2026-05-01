<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paseos y Guardería Devoto</title>
    <!-- Tailwind CSS para los estilos rápidos y accesibles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ajustes adicionales para legibilidad (Adultos mayores) */
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6; /* Fondo gris claro para que resalten las tarjetas blancas */
            color: #111827; /* Texto casi negro para máximo contraste */
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Botones más grandes al tocarlos en móviles */
        button, select, input, textarea {
            transition: all 0.2s ease-in-out;
        }
        button:active {
            transform: scale(0.98);
        }

        /* Ocultar barra de desplazamiento en el contenedor principal pero permitir scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Tarjetas con sombra suave para que se distingan del fondo */
        .card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 2px solid #e5e7eb;
        }

        /* Inputs y Selects enormes y claros */
        .input-grande {
            width: 100%;
            font-size: 1.25rem; /* Letra grande */
            padding: 1rem;
            border: 2px solid #9ca3af;
            border-radius: 0.75rem;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            background-color: white;
        }
        .input-grande:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        .etiqueta {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            display: block;
        }
    </style>
</head>
<body class="flex flex-col h-screen overflow-hidden">

    <!-- BARRA SUPERIOR -->
    <header class="bg-blue-700 text-white p-4 shadow-md z-10">
        <h1 class="text-3xl font-bold text-center tracking-wide">🐕 Paseos Devoto</h1>
        <p class="text-center text-blue-200 text-lg">Servicio exclusivo en Villa Devoto</p>
    </header>

    <!-- ÁREA CENTRAL DE CONTENIDO (Scrollable) -->
    <main id="app-content" class="flex-1 overflow-y-auto p-4 pb-28 no-scrollbar bg-gray-100">
        <!-- Las pantallas se inyectarán aquí vía JavaScript -->
    </main>

    <!-- MENÚ INFERIOR (Fijo, botones grandes) -->
    <nav class="bg-white border-t-4 border-gray-200 fixed bottom-0 w-full flex justify-around p-2 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <button onclick="navegar('inicio')" class="nav-btn flex flex-col items-center p-2 rounded-xl text-gray-500 w-1/4" id="nav-inicio">
            <span class="text-4xl mb-1">🏠</span>
            <span class="text-lg font-bold">Inicio</span>
        </button>
        <button onclick="navegar('perros')" class="nav-btn flex flex-col items-center p-2 rounded-xl text-gray-500 w-1/4" id="nav-perros">
            <span class="text-4xl mb-1">🐾</span>
            <span class="text-lg font-bold">Perros</span>
        </button>
        <button onclick="navegar('paseo')" class="nav-btn flex flex-col items-center p-2 rounded-xl text-gray-500 w-1/4" id="nav-paseo">
            <span class="text-4xl mb-1">📝</span>
            <span class="text-lg font-bold">Anotar</span>
        </button>
        <button onclick="navegar('cuentas')" class="nav-btn flex flex-col items-center p-2 rounded-xl text-gray-500 w-1/4" id="nav-cuentas">
            <span class="text-4xl mb-1">💰</span>
            <span class="text-lg font-bold">Plata</span>
        </button>
    </nav>

    <!-- AVISOS / MENSAJES (Notificaciones grandes) -->
    <div id="notificacion" class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white text-2xl font-bold px-6 py-4 rounded-2xl shadow-xl hidden z-50 text-center w-11/12 max-w-md">
        Mensaje
    </div>

    <script>
        // --- 1. BASE DE DATOS Y ESTADO (LocalStorage) ---
        const TARIFA_HORA = 9000;
        const DESCUENTO_MENSUAL = 0.20; // 20% de descuento si es abono mensual

        let appData = JSON.parse(localStorage.getItem('devoto_db')) || {
            perros: [],
            registros: []
        };

        // Perros de prueba si está vacío (Para que la persona entienda cómo funciona)
        if (appData.perros.length === 0) {
            appData.perros = [
                { id: 1, nombre: "Beto", dueno: "Doña Rosa", telefono: "11-2345-6789", vacunas: "Sí, al día", salud: "Sano. Cuidado con las escaleras.", comida: "Ya comió, solo agua.", zonas: ["Plaza Arenales"] },
                { id: 2, nombre: "Luna", dueno: "Carlos", telefono: "11-9876-5432", vacunas: "Le falta la antirrábica", salud: "Todo bien.", comida: "Darle un premio al volver.", zonas: ["Plaza Ricchieri", "Guardería"] }
            ];
            appData.registros = [
                { id: 1, perroId: 1, fecha: new Date().toISOString(), servicio: "Paseo", zona: "Plaza Arenales", horas: 1, total: 9000, tipoPago: "Suelto" }
            ];
            guardarDatos();
        }

        function guardarDatos() {
            localStorage.setItem('devoto_db', JSON.stringify(appData));
        }

        // --- 2. SISTEMA DE NAVEGACIÓN ---
        function navegar(pantalla, parametro = null) {
            // Actualizar botones del menú
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('text-blue-700', 'bg-blue-50');
                btn.classList.add('text-gray-500');
            });
            
            // Iluminar el botón activo si pertenece al menú inferior
            const btnActivo = document.getElementById(`nav-${pantalla}`);
            if(btnActivo) {
                btnActivo.classList.remove('text-gray-500');
                btnActivo.classList.add('text-blue-700', 'bg-blue-50');
            }

            const main = document.getElementById('app-content');
            main.innerHTML = ''; // Limpiar pantalla

            window.scrollTo(0, 0); // Volver arriba

            // Cargar la pantalla solicitada
            if (pantalla === 'inicio') renderInicio(main);
            if (pantalla === 'perros') renderListaPerros(main);
            if (pantalla === 'nuevo-perro') renderFormularioPerro(main);
            if (pantalla === 'ficha-perro') renderFichaPerro(main, parametro);
            if (pantalla === 'paseo') renderAnotarPaseo(main);
            if (pantalla === 'cuentas') renderCuentas(main);
        }

        function mostrarMensaje(texto, error = false) {
            const noti = document.getElementById('notificacion');
            noti.textContent = texto;
            noti.className = `fixed top-20 left-1/2 transform -translate-x-1/2 text-white text-2xl font-bold px-6 py-4 rounded-2xl shadow-xl z-50 text-center w-11/12 max-w-md transition-opacity ${error ? 'bg-red-600' : 'bg-green-600'}`;
            noti.style.display = 'block';
            setTimeout(() => { noti.style.display = 'none'; }, 3000);
        }

        // --- 3. PANTALLAS (VISTAS) ---

        // PANTALLA: INICIO
        function renderInicio(contenedor) {
            const totalPerros = appData.perros.length;
            const hoy = new Date().toLocaleDateString('es-AR');
            const paseosHoy = appData.registros.filter(r => new Date(r.fecha).toLocaleDateString('es-AR') === hoy).length;

            contenedor.innerHTML = `
                <h2 class="text-3xl font-bold mb-6 text-center">¡Hola! ¿Cómo estás hoy?</h2>
                
                <div class="card text-center bg-blue-50 border-blue-200">
                    <p class="text-xl text-gray-700">Hoy es:</p>
                    <p class="text-3xl font-bold text-blue-800">${hoy}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="card text-center flex flex-col justify-center cursor-pointer" onclick="navegar('perros')">
                        <span class="text-5xl mb-2">🐾</span>
                        <span class="text-4xl font-bold text-blue-700">${totalPerros}</span>
                        <span class="text-xl text-gray-600 font-semibold">Perros en total</span>
                    </div>
                    <div class="card text-center flex flex-col justify-center cursor-pointer" onclick="navegar('cuentas')">
                        <span class="text-5xl mb-2">🚶‍♂️</span>
                        <span class="text-4xl font-bold text-green-600">${paseosHoy}</span>
                        <span class="text-xl text-gray-600 font-semibold">Trabajos Hoy</span>
                    </div>
                </div>

                <button onclick="navegar('paseo')" class="mt-8 w-full bg-green-600 text-white text-3xl font-bold py-6 rounded-2xl shadow-lg border-b-4 border-green-800 flex justify-center items-center gap-4">
                    <span>📝</span> ¡Anotar un trabajo nuevo!
                </button>
            `;
        }

        // PANTALLA: LISTA DE PERROS
        function renderListaPerros(contenedor) {
            let html = `
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold">Mis Perros</h2>
                    <button onclick="navegar('nuevo-perro')" class="bg-blue-600 text-white text-xl font-bold py-3 px-6 rounded-xl shadow border-b-4 border-blue-800">
                        + Agregar
                    </button>
                </div>
            `;

            if (appData.perros.length === 0) {
                html += `<div class="card text-center text-2xl text-gray-500 py-10">Todavía no hay perros anotados.</div>`;
            } else {
                appData.perros.forEach(perro => {
                    html += `
                        <div class="card flex justify-between items-center cursor-pointer" onclick="navegar('ficha-perro', ${perro.id})">
                            <div class="flex items-center gap-4">
                                <span class="text-5xl">🐕</span>
                                <div>
                                    <h3 class="text-3xl font-bold text-gray-800">${perro.nombre}</h3>
                                    <p class="text-xl text-gray-500">De: ${perro.dueno}</p>
                                </div>
                            </div>
                            <span class="text-3xl text-blue-600 font-bold">VER ></span>
                        </div>
                    `;
                });
            }
            contenedor.innerHTML = html;
        }

        // PANTALLA: FORMULARIO NUEVO PERRO
        function renderFormularioPerro(contenedor) {
            contenedor.innerHTML = `
                <button onclick="navegar('perros')" class="text-blue-600 text-2xl font-bold mb-6 flex items-center gap-2">
                    <span>⬅</span> Volver atrás
                </button>
                <h2 class="text-3xl font-bold mb-6">Anotar un Nuevo Perro</h2>
                
                <div class="card">
                    <label class="etiqueta">Nombre del perro:</label>
                    <input type="text" id="p-nombre" class="input-grande" placeholder="Ej: Toby">

                    <label class="etiqueta">Nombre del dueño/a:</label>
                    <input type="text" id="p-dueno" class="input-grande" placeholder="Ej: María">

                    <label class="etiqueta">Teléfono del dueño:</label>
                    <input type="tel" id="p-tel" class="input-grande" placeholder="Ej: 11 4444 5555">

                    <label class="etiqueta">¿Tiene las vacunas al día?</label>
                    <select id="p-vacunas" class="input-grande">
                        <option value="Sí, todo al día">Sí, todo al día</option>
                        <option value="Faltan vacunas">Le faltan vacunas</option>
                        <option value="No estoy seguro">No me dijeron</option>
                    </select>

                    <label class="etiqueta">Notas de Salud / Comportamiento:</label>
                    <textarea id="p-salud" class="input-grande h-32" placeholder="Ej: Tira mucho de la correa, le duele la pata, etc."></textarea>

                    <label class="etiqueta">Alimentación / Comida:</label>
                    <textarea id="p-comida" class="input-grande h-32" placeholder="Ej: Darle agua, no darle galletitas."></textarea>

                    <button onclick="guardarPerro()" class="w-full bg-green-600 text-white text-3xl font-bold py-6 rounded-2xl shadow-lg border-b-4 border-green-800 mt-4">
                        💾 Guardar Ficha
                    </button>
                </div>
            `;
        }

        // ACCIÓN: GUARDAR PERRO
        function guardarPerro() {
            const nombre = document.getElementById('p-nombre').value.trim();
            if (!nombre) {
                mostrarMensaje("¡Falta el nombre del perro!", true);
                return;
            }

            const nuevoPerro = {
                id: Date.now(),
                nombre: nombre,
                dueno: document.getElementById('p-dueno').value.trim() || "Sin nombre",
                telefono: document.getElementById('p-tel').value.trim() || "Sin teléfono",
                vacunas: document.getElementById('p-vacunas').value,
                salud: document.getElementById('p-salud').value.trim() || "Ninguna nota",
                comida: document.getElementById('p-comida').value.trim() || "Sin indicaciones",
                zonas: [] // Se llenará a medida que pasee
            };

            appData.perros.push(nuevoPerro);
            guardarDatos();
            mostrarMensaje("¡Perro guardado con éxito!");
            navegar('perros');
        }

        // PANTALLA: FICHA DEL PERRO
        function renderFichaPerro(contenedor, perroId) {
            const perro = appData.perros.find(p => p.id === perroId);
            if(!perro) return navegar('perros');

            // Historial de paseos
            const historial = appData.registros.filter(r => r.perroId === perroId);
            const vecesPaseado = historial.length;
            
            // Extraer zonas únicas donde paseó
            const zonasVisitadas = [...new Set(historial.map(r => r.zona))].join(', ') || "Aún no ha paseado";

            contenedor.innerHTML = `
                <button onclick="navegar('perros')" class="text-blue-600 text-2xl font-bold mb-4 flex items-center gap-2">
                    <span>⬅</span> Volver a la lista
                </button>
                
                <div class="card border-t-8 border-blue-500">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-7xl">🐕</span>
                        <div>
                            <h2 class="text-4xl font-black text-gray-800">${perro.nombre}</h2>
                            <p class="text-2xl text-gray-600">Dueño/a: <b>${perro.dueno}</b></p>
                            <p class="text-xl text-blue-600 font-bold mt-1">📞 ${perro.telefono}</p>
                        </div>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-xl mb-4">
                        <span class="etiqueta text-gray-500">💉 Estado de Vacunas:</span>
                        <p class="text-2xl font-bold ${perro.vacunas.includes('Sí') ? 'text-green-700' : 'text-red-600'}">${perro.vacunas}</p>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-xl mb-4">
                        <span class="etiqueta text-gray-500">🩺 Salud y Comportamiento:</span>
                        <p class="text-xl text-gray-800">${perro.salud}</p>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-xl mb-4">
                        <span class="etiqueta text-gray-500">🥩 Comida e Hidratación:</span>
                        <p class="text-xl text-gray-800">${perro.comida}</p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl mb-6 border border-blue-200">
                        <span class="etiqueta text-blue-800">🚶‍♂️ Historial de Trabajos:</span>
                        <p class="text-xl text-gray-800">Lo sacaste <b>${vecesPaseado} veces</b>.</p>
                        <p class="text-lg text-gray-600 mt-2">Zonas: ${zonasVisitadas}</p>
                    </div>

                    <button onclick="eliminarPerro(${perro.id})" class="w-full bg-red-100 text-red-700 text-xl font-bold py-4 rounded-xl border-2 border-red-300">
                        Borrar esta ficha
                    </button>
                </div>
            `;
        }

        function eliminarPerro(id) {
            if(confirm("¿Estás seguro de que quieres borrar a este perro? No se puede deshacer.")) {
                appData.perros = appData.perros.filter(p => p.id !== id);
                appData.registros = appData.registros.filter(r => r.perroId !== id); // Borrar su historial
                guardarDatos();
                mostrarMensaje("Ficha borrada");
                navegar('perros');
            }
        }

        // PANTALLA: ANOTAR PASEO O GUARDERÍA
        function renderAnotarPaseo(contenedor) {
            if (appData.perros.length === 0) {
                contenedor.innerHTML = `
                    <h2 class="text-3xl font-bold mb-6">Anotar un Trabajo</h2>
                    <div class="card text-center">
                        <p class="text-2xl mb-4">Primero tenés que anotar un perro en el sistema.</p>
                        <button onclick="navegar('nuevo-perro')" class="bg-blue-600 text-white text-2xl font-bold py-4 px-6 rounded-xl w-full">Ir a crear perro</button>
                    </div>`;
                return;
            }

            let opcionesPerros = appData.perros.map(p => `<option value="${p.id}">${p.nombre} (de ${p.dueno})</option>`).join('');

            contenedor.innerHTML = `
                <h2 class="text-3xl font-bold mb-6">📝 Anotar Trabajo Terminado</h2>
                
                <div class="card bg-yellow-50 border-yellow-300 mb-4 p-4 text-center">
                    <p class="text-lg font-bold text-yellow-800">📍 Recordatorio: Servicio solo en Villa Devoto.</p>
                </div>

                <div class="card">
                    <label class="etiqueta">¿A qué perro cuidaste o paseaste?</label>
                    <select id="reg-perro" class="input-grande bg-blue-50 font-bold">
                        ${opcionesPerros}
                    </select>

                    <label class="etiqueta">¿Qué trabajo fue?</label>
                    <div class="flex gap-4 mb-6">
                        <label class="flex-1 text-center bg-gray-100 p-4 rounded-xl border-2 border-gray-300 cursor-pointer text-2xl" id="lbl-paseo">
                            <input type="radio" name="reg-servicio" value="Paseo" checked class="hidden" onchange="seleccionarServicio()">
                            🚶‍♂️ Paseo
                        </label>
                        <label class="flex-1 text-center bg-gray-100 p-4 rounded-xl border-2 border-gray-300 cursor-pointer text-2xl" id="lbl-guarderia">
                            <input type="radio" name="reg-servicio" value="Guardería" class="hidden" onchange="seleccionarServicio()">
                            🏠 Guardería
                        </label>
                    </div>

                    <label class="etiqueta">¿Por dónde? (Zona de Devoto)</label>
                    <select id="reg-zona" class="input-grande">
                        <option value="Plaza Arenales">Plaza Arenales</option>
                        <option value="Plaza Ricchieri">Plaza Ricchieri</option>
                        <option value="Devoto R (Calles)">Devoto R (Calles)</option>
                        <option value="Estación Devoto">Estación Devoto</option>
                        <option value="En mi casa (Guardería)">En mi casa (Guardería)</option>
                    </select>

                    <label class="etiqueta">¿Cuántas horas fueron?</label>
                    <div class="flex items-center gap-4 mb-6">
                        <button onclick="cambiarHoras(-1)" class="bg-gray-300 text-4xl w-16 h-16 rounded-xl font-bold">-</button>
                        <input type="number" id="reg-horas" value="1" min="1" class="input-grande m-0 text-center text-3xl font-bold w-24" readonly>
                        <button onclick="cambiarHoras(1)" class="bg-gray-300 text-4xl w-16 h-16 rounded-xl font-bold">+</button>
                    </div>

                    <label class="etiqueta">Tipo de cobro:</label>
                    <select id="reg-pago" class="input-grande" onchange="calcularTotal()">
                        <option value="Suelto">Paseo Suelto (Normal)</option>
                        <option value="Mensual">Abono Mensual (20% descuento)</option>
                    </select>

                    <div class="bg-green-100 border-2 border-green-400 p-4 rounded-xl text-center mb-6">
                        <span class="text-xl text-green-800 font-bold block mb-1">El cliente tiene que pagar:</span>
                        <span id="reg-total" class="text-5xl font-black text-green-700">$9000</span>
                    </div>

                    <button onclick="guardarRegistro()" class="w-full bg-blue-600 text-white text-3xl font-bold py-6 rounded-2xl shadow-lg border-b-4 border-blue-800">
                        ✅ Guardar y Cobrar
                    </button>
                </div>
            `;
            seleccionarServicio(); // Para pintar el botón por defecto
            calcularTotal();
        }

        function cambiarHoras(cantidad) {
            const input = document.getElementById('reg-horas');
            let valor = parseInt(input.value) + cantidad;
            if (valor < 1) valor = 1;
            input.value = valor;
            calcularTotal();
        }

        function seleccionarServicio() {
            const servicio = document.querySelector('input[name="reg-servicio"]:checked').value;
            const lblPaseo = document.getElementById('lbl-paseo');
            const lblGuarderia = document.getElementById('lbl-guarderia');
            const selectZona = document.getElementById('reg-zona');

            if (servicio === 'Paseo') {
                lblPaseo.classList.add('bg-blue-100', 'border-blue-500', 'text-blue-800', 'font-bold');
                lblGuarderia.classList.remove('bg-blue-100', 'border-blue-500', 'text-blue-800', 'font-bold');
                selectZona.value = "Plaza Arenales"; // Reset
            } else {
                lblGuarderia.classList.add('bg-blue-100', 'border-blue-500', 'text-blue-800', 'font-bold');
                lblPaseo.classList.remove('bg-blue-100', 'border-blue-500', 'text-blue-800', 'font-bold');
                selectZona.value = "En mi casa (Guardería)";
            }
        }

        function calcularTotal() {
            const horas = parseInt(document.getElementById('reg-horas').value) || 1;
            const tipoPago = document.getElementById('reg-pago').value;
            
            let total = horas * TARIFA_HORA;
            
            if (tipoPago === 'Mensual') {
                total = total - (total * DESCUENTO_MENSUAL); // Aplica 20% descuento
            }

            document.getElementById('reg-total').innerText = "$" + total;
            return total; // Para usar al guardar
        }

        function guardarRegistro() {
            const nuevoRegistro = {
                id: Date.now(),
                perroId: parseInt(document.getElementById('reg-perro').value),
                fecha: new Date().toISOString(),
                servicio: document.querySelector('input[name="reg-servicio"]:checked').value,
                zona: document.getElementById('reg-zona').value,
                horas: parseInt(document.getElementById('reg-horas').value),
                tipoPago: document.getElementById('reg-pago').value,
                total: calcularTotal()
            };

            appData.registros.push(nuevoRegistro);
            guardarDatos();
            mostrarMensaje("¡Trabajo guardado! La plata se sumó a tus cuentas.");
            navegar('inicio');
        }

        // PANTALLA: MIS CUENTAS (Finanzas)
        function renderCuentas(contenedor) {
            // Calcular ganancias del mes actual
            const mesActual = new Date().getMonth();
            const anioActual = new Date().getFullYear();
            
            let gananciaMes = 0;
            const trabajosMes = appData.registros.filter(r => {
                const fechaR = new Date(r.fecha);
                return fechaR.getMonth() === mesActual && fechaR.getFullYear() === anioActual;
            });

            trabajosMes.forEach(t => gananciaMes += t.total);

            // Generar lista de los últimos 10 trabajos
            let listaTrabajos = '';
            const ultimos = [...appData.registros].reverse().slice(0, 10); // Últimos 10

            if(ultimos.length === 0) {
                listaTrabajos = `<p class="text-xl text-gray-500 text-center py-6">Todavía no hay trabajos cobrados.</p>`;
            } else {
                ultimos.forEach(trabajo => {
                    const perro = appData.perros.find(p => p.id === trabajo.perroId);
                    const nombrePerro = perro ? perro.nombre : "Perro borrado";
                    const fecha = new Date(trabajo.fecha).toLocaleDateString('es-AR', {day: '2-digit', month: '2-digit'});
                    
                    listaTrabajos += `
                        <div class="flex justify-between items-center border-b border-gray-200 py-4">
                            <div>
                                <p class="text-xl font-bold text-gray-800">${trabajo.servicio} - ${nombrePerro}</p>
                                <p class="text-lg text-gray-500">🗓️ ${fecha} | ⏱️ ${trabajo.horas} hora(s)</p>
                            </div>
                            <div class="text-2xl font-black text-green-600">
                                +$${trabajo.total}
                            </div>
                        </div>
                    `;
                });
            }

            contenedor.innerHTML = `
                <h2 class="text-3xl font-bold mb-6">💰 Mis Cuentas</h2>

                <div class="card bg-green-600 text-white text-center shadow-xl border-none">
                    <p class="text-2xl mb-2 text-green-100 font-semibold">Plata ganada este mes</p>
                    <p class="text-6xl font-black mb-2">$${gananciaMes}</p>
                    <p class="text-lg text-green-200">Tarifa base: $9000/h</p>
                </div>

                <h3 class="text-2xl font-bold mb-4 mt-8 text-gray-700">Últimos trabajos cobrados:</h3>
                <div class="card">
                    ${listaTrabajos}
                </div>
            `;
        }

        // --- INICIAR APP ---
        // Cargar la pantalla de inicio al abrir la aplicación
        window.onload = () => {
            navegar('inicio');
        };

    </script>
</body>
</html>
