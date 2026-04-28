--
-- PostgreSQL database dump
--



-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-04-27 17:52:43

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 219 (class 1259 OID 33439)
-- Name: catalogo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalogo (
    id integer NOT NULL,
    nombre character varying(50)
);


ALTER TABLE public.catalogo OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 33443)
-- Name: catalogo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalogo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalogo_id_seq OWNER TO postgres;

--
-- TOC entry 5046 (class 0 OID 0)
-- Dependencies: 220
-- Name: catalogo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalogo_id_seq OWNED BY public.catalogo.id;


--
-- TOC entry 221 (class 1259 OID 33444)
-- Name: registroproducto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.registroproducto (
    id integer NOT NULL,
    precio numeric(6,2),
    fecharegistro date,
    unidadmedida character varying(20),
    cantidad numeric(5,2),
    idubicacion integer,
    idcatalogo integer
);


ALTER TABLE public.registroproducto OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 33448)
-- Name: registroproducto_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.registroproducto_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.registroproducto_id_seq OWNER TO postgres;

--
-- TOC entry 5047 (class 0 OID 0)
-- Dependencies: 222
-- Name: registroproducto_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.registroproducto_id_seq OWNED BY public.registroproducto.id;


--
-- TOC entry 223 (class 1259 OID 33449)
-- Name: ubicacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ubicacion (
    id integer NOT NULL,
    latitud numeric(7,5),
    longitud numeric(8,5),
    tienda character varying(100),
    direccion character varying(255)
);


ALTER TABLE public.ubicacion OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 33453)
-- Name: ubicacion_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ubicacion_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ubicacion_id_seq OWNER TO postgres;

--
-- TOC entry 5048 (class 0 OID 0)
-- Dependencies: 224
-- Name: ubicacion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ubicacion_id_seq OWNED BY public.ubicacion.id;


--
-- TOC entry 225 (class 1259 OID 33454)
-- Name: votos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.votos (
    id integer NOT NULL,
    voto boolean NOT NULL,
    idregistroproducto integer
);


ALTER TABLE public.votos OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 33459)
-- Name: votos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.votos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.votos_id_seq OWNER TO postgres;

--
-- TOC entry 5049 (class 0 OID 0)
-- Dependencies: 226
-- Name: votos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.votos_id_seq OWNED BY public.votos.id;


--
-- TOC entry 4871 (class 2604 OID 33460)
-- Name: catalogo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalogo ALTER COLUMN id SET DEFAULT nextval('public.catalogo_id_seq'::regclass);


--
-- TOC entry 4872 (class 2604 OID 33461)
-- Name: registroproducto id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registroproducto ALTER COLUMN id SET DEFAULT nextval('public.registroproducto_id_seq'::regclass);


--
-- TOC entry 4873 (class 2604 OID 33462)
-- Name: ubicacion id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ubicacion ALTER COLUMN id SET DEFAULT nextval('public.ubicacion_id_seq'::regclass);


--
-- TOC entry 4874 (class 2604 OID 33463)
-- Name: votos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos ALTER COLUMN id SET DEFAULT nextval('public.votos_id_seq'::regclass);


--
-- TOC entry 5033 (class 0 OID 33439)
-- Dependencies: 219
-- Data for Name: catalogo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalogo (id, nombre) FROM stdin;
1	Jitomate
2	Arroz
3	Frijol
4	Huevo
5	Lechuga
6	Tortilla
7	Aceite
8	Leche
9	Manzana
10	Limon
11	Miel
12	Pera
13	Nuez
14	Almendra
\.


--
-- TOC entry 5035 (class 0 OID 33444)
-- Dependencies: 221
-- Data for Name: registroproducto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.registroproducto (id, precio, fecharegistro, unidadmedida, cantidad, idubicacion, idcatalogo) FROM stdin;
1	32.50	2026-04-06	kg	1.00	1	1
2	60.00	2026-04-06	kg	2.00	3	1
3	35.20	2026-04-06	kg	1.00	5	1
4	94.50	2026-04-07	kg	3.00	7	1
5	63.50	2026-04-07	kg	2.00	9	1
6	172.50	2026-04-08	kg	5.00	11	1
7	63.40	2026-04-08	kg	2.00	13	1
8	33.20	2026-04-08	kg	1.00	15	1
9	98.50	2026-04-09	kg	3.00	2	1
10	125.50	2026-04-09	kg	4.00	4	1
11	24.50	2026-04-06	kg	1.00	2	2
12	46.00	2026-04-06	kg	2.00	4	2
13	22.90	2026-04-06	kg	0.80	6	2
14	68.00	2026-04-07	kg	3.00	8	2
15	25.50	2026-04-07	kg	1.00	10	2
16	26.50	2026-04-07	kg	1.00	12	2
17	48.00	2026-04-08	kg	2.00	14	2
18	36.50	2026-04-08	kg	1.50	15	2
19	67.50	2026-04-09	kg	3.00	13	2
20	26.00	2026-04-10	kg	1.00	11	2
21	38.00	2026-04-06	kg	1.20	1	3
22	75.50	2026-04-06	kg	2.00	2	3
23	36.20	2026-04-07	kg	1.00	3	3
24	112.00	2026-04-07	kg	3.00	4	3
25	40.00	2026-04-08	kg	1.00	5	3
26	43.00	2026-04-08	kg	1.20	6	3
27	77.50	2026-04-08	kg	2.00	7	3
28	38.00	2026-04-09	kg	1.00	8	3
29	110.50	2026-04-09	kg	3.00	9	3
30	42.50	2026-04-10	kg	1.00	10	3
31	42.00	2026-04-06	kg	1.00	15	4
32	82.00	2026-04-06	kg	2.00	12	4
33	45.50	2026-04-06	kg	1.00	9	4
34	130.00	2026-04-07	kg	3.00	6	4
35	71.50	2026-04-07	kg	1.50	3	4
36	44.00	2026-04-07	kg	1.00	1	4
37	85.00	2026-04-08	kg	2.00	4	4
38	47.50	2026-04-08	kg	1.00	7	4
39	131.50	2026-04-09	kg	3.00	10	4
40	73.50	2026-04-09	kg	1.50	13	4
41	18.50	2026-04-06	pieza	1.00	2	5
42	36.00	2026-04-06	pieza	2.00	5	5
43	19.90	2026-04-07	pieza	1.00	7	5
44	22.00	2026-04-07	pieza	1.00	9	5
45	40.00	2026-04-07	pieza	2.00	11	5
46	19.50	2026-04-08	pieza	1.00	13	5
47	37.50	2026-04-08	pieza	2.00	15	5
48	67.90	2026-04-08	pieza	3.00	1	5
49	70.50	2026-04-08	pieza	3.00	6	5
50	42.00	2026-04-09	pieza	2.00	12	5
51	33.00	2026-04-06	kg	1.50	1	6
52	44.00	2026-04-06	kg	2.00	15	6
53	24.00	2026-04-06	kg	1.00	2	6
54	66.00	2026-04-07	kg	3.00	14	6
55	23.50	2026-04-07	kg	1.00	3	6
56	32.00	2026-04-07	kg	1.50	13	6
57	44.00	2026-04-08	kg	2.00	4	6
58	26.50	2026-04-08	kg	1.00	12	6
59	66.00	2026-04-09	kg	3.00	5	6
60	14.50	2026-04-09	kg	0.50	11	6
61	73.90	2026-04-06	litro	1.50	15	7
62	95.00	2026-04-06	litro	2.00	10	7
63	42.50	2026-04-06	litro	1.00	3	7
64	140.00	2026-04-06	litro	3.00	8	7
65	50.00	2026-04-06	litro	1.00	12	7
66	38.50	2026-04-07	litro	0.85	1	7
67	115.00	2026-04-07	litro	2.50	4	7
68	46.00	2026-04-08	litro	1.00	6	7
69	24.00	2026-04-08	litro	0.50	14	7
70	185.00	2026-04-09	litro	4.00	2	7
71	27.50	2026-04-06	litro	1.00	1	8
72	54.00	2026-04-06	litro	2.00	4	8
73	50.00	2026-04-06	litro	1.50	14	8
74	81.00	2026-04-06	litro	3.00	7	8
75	29.00	2026-04-06	litro	1.00	11	8
76	28.90	2026-04-07	litro	1.00	3	8
77	13.50	2026-04-07	litro	0.50	15	8
78	56.00	2026-04-08	litro	2.00	9	8
79	84.50	2026-04-08	litro	3.00	5	8
80	31.00	2026-04-09	litro	1.00	10	8
81	65.50	2026-04-06	kg	1.30	5	9
82	88.00	2026-04-06	kg	2.00	9	9
83	41.50	2026-04-06	kg	1.00	2	9
84	250.00	2026-04-06	kg	6.00	13	9
85	48.00	2026-04-06	kg	1.00	6	9
86	12.50	2026-04-07	kg	0.25	1	9
87	34.00	2026-04-07	kg	0.75	4	9
88	110.00	2026-04-08	kg	2.50	8	9
89	22.00	2026-04-08	kg	0.50	11	9
90	65.00	2026-04-09	kg	1.50	15	9
91	30.00	2026-04-06	kg	1.00	10	10
92	58.50	2026-04-06	kg	2.00	1	10
93	14.00	2026-04-06	kg	0.50	8	10
94	85.00	2026-04-06	kg	3.00	15	10
95	47.50	2026-04-06	kg	1.50	3	10
96	9.00	2026-04-07	kg	0.30	12	10
97	42.00	2026-04-07	kg	1.50	5	10
98	26.50	2026-04-08	kg	1.00	2	10
99	55.00	2026-04-08	kg	2.00	14	10
100	21.00	2026-04-09	kg	0.75	7	10
101	100.00	2026-04-12	kg	1.00	16	11
102	65.00	2026-04-12	kg	2.00	16	12
103	100.00	2026-04-12	kg	1.00	16	11
104	65.00	2026-04-12	kg	2.00	16	12
105	100.00	2026-04-12	kg	1.00	16	11
106	65.00	2026-04-12	kg	2.00	16	12
107	100.00	2026-04-12	kg	1.00	16	11
108	65.00	2026-04-12	kg	2.00	16	12
109	100.00	2026-04-12	kg	1.00	16	11
110	65.00	2026-04-12	kg	2.00	16	12
111	100.00	2026-04-12	kg	1.00	16	11
112	65.00	2026-04-12	kg	2.00	16	12
113	100.00	2026-04-12	kg	1.00	16	11
114	65.00	2026-04-12	kg	2.00	16	12
115	100.00	2026-04-12	kg	1.00	17	11
116	65.00	2026-04-12	kg	2.00	17	12
117	1.00	2026-04-12	kg	1.00	18	11
118	1.00	2026-04-12	kg	1.00	18	11
119	200.00	2026-04-12	kg	2.00	18	13
120	1.00	2026-04-12	kg	1.00	19	11
121	150.00	2026-04-12	kg	1.00	19	14
\.


--
-- TOC entry 5037 (class 0 OID 33449)
-- Dependencies: 223
-- Data for Name: ubicacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ubicacion (id, latitud, longitud, tienda, direccion) FROM stdin;
1	20.64879	-103.31021	Walmart Revolución	Calle Ramón López Velarde 821, Lomas del Paradero, 44840 Guadalajara, Jal.
2	20.64720	-103.31901	Soriana Híper Forum Tlaquepaque	Blvd. Gral. Marcelino García Barragán 2077, Prados del Nilo, 44840 Guadalajara, Jal., México
3	20.65533	-103.32024	Farmacias Guadalajara Universitaria	Calz. Olímpica 613, Olímpica, 44430 Guadalajara, Jal., México
4	20.66399	-103.33166	Abarrotes Castillo	Urano 1049, Obrera, 44420 Guadalajara, Jal., México
5	20.63763	-103.29861	Abarrotes Adriana	C. Clavel 134a, Los Altos, 45520 San Pedro Tlaquepaque, Jal., México
6	20.61746	-103.32071	Soriana Súper Espacio Tlaquepaque	C. Carr. a Chapala 3221, El Tapatío, 45580 San Pedro Tlaquepaque, Jal., México
7	20.63076	-103.33191	Abarrotes Libertad	Refinería 1252, Lázaro Cárdenas, 44490 Guadalajara, Jal., México
8	20.63254	-103.26252	OXXO Loma Norte GDL	Paseo Lomas Del Norte 7886 Esquina Circuito Loma Norte, Loma Dorada, Secc D, 45402 Tonalá, Jal., México
9	20.63381	-103.24589	Tienda Carmen	Reina Tzapozintli 416A, Pachaguillo, 45400 Tonalá, Jal., México
10	20.58941	-103.27137	Mini Super Arboledas	C. Avellano 134, 45638 San Pedro Tlaquepaque, Jal., México
11	20.63210	-103.29503	Bodega Aurrera, Revolución	Av. Niños Héroes 720, Sector Hidalgo, 45540 San Pedro Tlaquepaque, Jal., México
12	20.61932	-103.07440	Mi Bodega Zapotlanejo	Solidaridad 49, Loma Dorada, 45430 Zapotlanejo, Jal., México
13	20.62280	-103.06324	Minisuper Olivares	C. Juárez 198, Santuario, 45430 Zapotlanejo, Jal., México
14	20.62289	-103.07197	OXXO Zapotlanejo Centro	Miguel Hidalgo Y Costilla Y Revolucion 150, C. Hidalgo 150, 45430 Zapotlanejo, Jal., México
15	20.62989	-103.25748	Walmart Tonalá	Loma Dorada, Av Río Nilo 8096, Loma Dorada Delegación B, 45402 Tonalá, Jal., México
16	20.64263	-103.30326	Sin Nombre	Tienda prueba
17	20.64263	-103.30326	Tienda prueba	San Juan, 45500 San Pedro Tlaquepaque, Jal., México
18	20.64263	-103.30326	Tienda prueba	Dirección no encontrada
19	20.65526	-103.32549	prueba tienda	MM4F+4R Guadalajara, Jalisco, Mexico
\.


--
-- TOC entry 5039 (class 0 OID 33454)
-- Dependencies: 225
-- Data for Name: votos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.votos (id, voto, idregistroproducto) FROM stdin;
1	t	1
2	t	1
3	f	1
4	t	1
5	t	1
6	f	1
7	t	1
8	t	1
9	t	15
10	t	15
11	f	15
12	f	15
13	t	15
14	t	15
15	t	15
16	f	15
17	t	15
18	t	15
19	f	50
20	f	50
21	t	50
22	f	50
23	f	50
24	t	50
25	f	50
26	t	3
27	t	3
28	f	3
29	t	3
30	t	3
31	t	12
32	t	12
33	t	12
34	f	12
35	t	25
36	t	25
37	f	25
38	t	25
39	t	25
40	f	25
41	f	33
42	f	33
43	t	33
44	f	33
45	f	33
46	t	40
47	t	40
48	t	40
49	t	40
50	t	60
51	t	60
52	f	60
53	t	60
54	t	75
55	t	75
56	t	75
57	f	75
58	t	75
59	t	2
60	t	2
61	t	5
62	f	5
63	t	8
64	t	8
65	f	8
66	t	10
67	t	10
68	t	18
69	t	18
70	t	20
71	f	20
72	t	22
73	t	22
74	t	28
75	t	28
76	t	30
77	f	30
78	t	35
79	t	35
80	t	44
81	t	44
82	t	48
83	t	48
84	t	52
85	t	52
86	t	55
87	f	55
88	t	58
89	f	58
90	t	62
91	t	62
92	t	66
93	t	66
94	t	70
95	t	70
96	t	77
97	t	77
98	t	80
99	t	80
100	t	85
101	t	85
102	f	85
103	t	88
104	f	88
105	t	90
106	t	90
107	t	92
108	t	92
109	f	95
110	f	95
111	t	95
112	t	98
113	t	98
114	t	99
115	t	100
116	t	1
117	f	15
118	t	25
119	f	33
120	t	40
121	t	60
122	t	75
123	f	85
124	t	95
125	t	50
126	t	2
127	f	2
128	f	2
129	t	41
130	f	42
131	t	47
\.


--
-- TOC entry 5050 (class 0 OID 0)
-- Dependencies: 220
-- Name: catalogo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalogo_id_seq', 14, true);


--
-- TOC entry 5051 (class 0 OID 0)
-- Dependencies: 222
-- Name: registroproducto_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.registroproducto_id_seq', 124, true);


--
-- TOC entry 5052 (class 0 OID 0)
-- Dependencies: 224
-- Name: ubicacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ubicacion_id_seq', 19, true);


--
-- TOC entry 5053 (class 0 OID 0)
-- Dependencies: 226
-- Name: votos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.votos_id_seq', 134, true);


--
-- TOC entry 4876 (class 2606 OID 33465)
-- Name: catalogo catalogo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalogo
    ADD CONSTRAINT catalogo_pkey PRIMARY KEY (id);


--
-- TOC entry 4878 (class 2606 OID 33467)
-- Name: registroproducto registroproducto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registroproducto
    ADD CONSTRAINT registroproducto_pkey PRIMARY KEY (id);


--
-- TOC entry 4880 (class 2606 OID 33469)
-- Name: ubicacion ubicacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ubicacion
    ADD CONSTRAINT ubicacion_pkey PRIMARY KEY (id);


--
-- TOC entry 4882 (class 2606 OID 33471)
-- Name: votos votos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos
    ADD CONSTRAINT votos_pkey PRIMARY KEY (id);


--
-- TOC entry 4885 (class 2606 OID 33472)
-- Name: votos fk_votos_registro_producto; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos
    ADD CONSTRAINT fk_votos_registro_producto FOREIGN KEY (idregistroproducto) REFERENCES public.registroproducto(id) ON DELETE CASCADE;


--
-- TOC entry 4883 (class 2606 OID 33477)
-- Name: registroproducto registroproducto_idcatalogo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registroproducto
    ADD CONSTRAINT registroproducto_idcatalogo_fkey FOREIGN KEY (idcatalogo) REFERENCES public.catalogo(id);


--
-- TOC entry 4884 (class 2606 OID 33482)
-- Name: registroproducto registroproducto_idubicacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registroproducto
    ADD CONSTRAINT registroproducto_idubicacion_fkey FOREIGN KEY (idubicacion) REFERENCES public.ubicacion(id);


-- Completed on 2026-04-27 17:52:43

--
-- PostgreSQL database dump complete
--


