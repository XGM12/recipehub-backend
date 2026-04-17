CREATE DATABASE  IF NOT EXISTS `recipehub` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `recipehub`;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(500),
    prep_time_minutes INT NOT NULL,
    category ENUM('Entrante', 'Principal', 'Postre') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity VARCHAR(50) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS recipe_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    step_order INT NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_favourites (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

INSERT INTO ingredients (name) VALUES
                                   ('Harina'),       -- 1
                                   ('Huevos'),       -- 2
                                   ('Leche'),        -- 3
                                   ('Mantequilla'),  -- 4
                                   ('Azúcar'),       -- 5
                                   ('Sal'),          -- 6
                                   ('Aceite de oliva'), -- 7
                                   ('Ajo'),          -- 8
                                   ('Cebolla'),      -- 9
                                   ('Tomate'),       -- 10
                                   ('Pollo'),        -- 11
                                   ('Arroz'),        -- 12
                                   ('Pasta'),        -- 13
                                   ('Queso'),        -- 14
                                   ('Nata'),         -- 15
                                   ('Limón'),        -- 16
                                   ('Chocolate'),    -- 17
                                   ('Levadura'),     -- 18
                                   ('Pimiento'),     -- 19
                                   ('Patata');       -- 20

-- Users
INSERT INTO users (email, password, name) VALUES
    ('test@test.com', 'test', 'Test');

-- Recetas del sistema (created_by = NULL), 10 de cada
INSERT INTO recipes (created_by, name, image_url, prep_time_minutes, category) VALUES
-- Entrantes sistema
(NULL, 'Hummus casero',          'https://deliciaskitchen.b-cdn.net/wp-content/uploads/2020/09/hummus-casero-de-garbanzos-receta-facil-1170x781.webp', 15, 'Entrante'),
(NULL, 'Patatas bravas',         'https://blog.amigofoods.com/wp-content/uploads/2020/06/patatas-bravas.jpg', 35, 'Entrante'),
(NULL, 'Sopa de cebolla',        'https://chefgoya.b-cdn.net/wp-content/uploads/2024/12/Receta-de-Sopa-de-Cebolla-e1734658710684-678x470.png', 40, 'Entrante'),
(NULL, 'Aguacate con gambas',    'https://recetasdecocina.elmundo.es/wp-content/uploads/2022/11/aguacates-rellenos-de-gambas-receta.jpg', 10, 'Entrante'),
(NULL, 'Pimientos rellenos',     'https://unareceta.com/wp-content/uploads/2016/10/pimientos-rellenos.jpg', 45, 'Entrante'),
(NULL, 'Crema de calabaza',      'https://recetastips.com/wp-content/uploads/2019/10/Crema-de-Calabaza-1.jpg', 30, 'Entrante'),
(NULL, 'Ensalada de pasta',      'https://recetasdecocina.elmundo.es/wp-content/uploads/2021/07/ensalada-de-pasta.jpg', 20, 'Entrante'),
(NULL, 'Tostas de salmón',       'https://recetinas.com/wp-content/uploads/2021/02/tostas-de-salmon-con-aguacate.jpg', 10, 'Entrante'),
(NULL, 'Boquerones en vinagre',  'https://recetasdecocina.elmundo.es/wp-content/uploads/2024/06/boquerones-en-vinagre-receta-1024x683.jpg', 15, 'Entrante'),
(NULL, 'Crema de champiñones',   'https://recetinas.com/wp-content/uploads/2017/07/crema-de-champinones.jpg', 25, 'Entrante'),
-- Principales sistema
(NULL, 'Paella valenciana',      'https://www.visitvalencia.com/sites/default/files/styles/sidebar_logo/public/media/media-images/images/Paella-VV-19877_1024-%20Foto_Mike_Water.jpg?itok=Lg7CsoAo', 60, 'Principal'),
(NULL, 'Lasaña boloñesa',        'https://cdn0.recetasgratis.net/es/posts/7/6/5/lasana_bolonesa_facil_38567_1200.webp', 75, 'Principal'),
(NULL, 'Merluza en salsa verde', 'https://imag.bonviveur.com/merluza-en-salsa-verde-receta-tradicional.webp', 30, 'Principal'),
(NULL, 'Carne guisada',          'https://cdn.casaeculinaria.com/wp-content/uploads/2023/03/13121047/Guisado-768x432.jpg', 90, 'Principal'),
(NULL, 'Risotto de setas',       'https://www.annarecetasfaciles.com/files/risotto-setas-1-2-815x458.jpg', 40, 'Principal'),
(NULL, 'Bacalao al pil pil',     'https://imag.bonviveur.com/bacalao-al-pil-pil.webp', 45, 'Principal'),
(NULL, 'Fabada asturiana',       'https://imag.bonviveur.com/fabada-asturiana.webp', 120, 'Principal'),
(NULL, 'Pollo al curry',         'https://www.alimentaitaly.com/img/cms/pollo%20curry%20LARGE.jpg', 40, 'Principal'),
(NULL, 'Espaguetis boloñesa',    'https://www.unileverfoodsolutions.com.co/dam/global-ufs/mcos/NOLA/calcmenu/recipes/col-recipies/fruco-tomate-cocineros/BOLO%C3%91ESA%201200x709.png', 45, 'Principal'),
(NULL, 'Arroz tres delicias',    'https://cdn0.recetasgratis.net/es/posts/5/1/4/arroz_tres_delicias_chino_8415_1200.webp', 25, 'Principal'),
-- Postres sistema
(NULL, 'Tiramisú',               'https://s2.elespanol.com/2023/12/15/cocinillas/recetas/817428853_238446853_1706x960.jpg', 30, 'Postre'),
(NULL, 'Tarta de queso',         'https://www.annarecetasfaciles.com/files/tarta-de-queso-de-la-vina-815x458.jpg', 55, 'Postre'),
(NULL, 'Crema catalana',         'https://www.eladerezo.com/wp-content/uploads/2015/04/receta-de-crema-catalana-1200x673.jpg', 35, 'Postre'),
(NULL, 'Bizcocho de limón',      'https://cocinaconnoelia.com/wp-content/uploads/2024/02/Bizcocho-de-limon-600x400.webp', 50, 'Postre'),
(NULL, 'Mousse de chocolate',    'https://cdn.casaeculinaria.com/wp-content/uploads/2023/01/28112925/Mousse-de-chocolate-600x400.webp', 20, 'Postre'),
(NULL, 'Arroz con leche',        'https://recetasdetiameche.com/wp-content/uploads/2016/11/arroz-con-leche-768x576.jpg', 40, 'Postre'),
(NULL, 'Tarta de manzana',       'https://imag.bonviveur.com/tarta-de-manzana.webp', 65, 'Postre'),
(NULL, 'Coulant de chocolate',   'https://www.eladerezo.com/wp-content/uploads/2016/03/coulant-de-chocolate-3-600x400.jpg', 20, 'Postre'),
(NULL, 'Panna cotta',            'https://www.cucinabyelena.com/wp-content/uploads/2023/06/Panna-Cotta-20-scaled.jpg', 15, 'Postre'),
(NULL, 'Magdalenas caseras',     'https://www.mehueleaquemao.com/wp-content/uploads/2017/02/Magdalenas-caseras-receta.jpg', 35, 'Postre');

-- 1 Hummus casero
INSERT INTO recipe_ingredients VALUES (1,8,'2 dientes'),(1,16,'1 unidad'),(1,7,'3 cucharadas'),(1,6,'al gusto');
-- 2 Patatas bravas
INSERT INTO recipe_ingredients VALUES (2,20,'4 patatas'),(2,7,'150ml'),(2,10,'2 tomates'),(2,8,'2 dientes'),(2,6,'al gusto');
-- 3 Sopa de cebolla
INSERT INTO recipe_ingredients VALUES (3,9,'4 cebollas'),(3,4,'40g'),(3,14,'100g'),(3,6,'al gusto');
-- 4 Aguacate con gambas
INSERT INTO recipe_ingredients VALUES (4,16,'1 unidad'),(4,7,'2 cucharadas'),(4,6,'al gusto');
-- 5 Pimientos rellenos
INSERT INTO recipe_ingredients VALUES (5,19,'4 unidades'),(5,12,'150g'),(5,9,'1 unidad'),(5,10,'2 tomates'),(5,7,'2 cucharadas'),(5,6,'al gusto');
-- 6 Crema de calabaza
INSERT INTO recipe_ingredients VALUES (6,9,'1 unidad'),(6,4,'30g'),(6,15,'100ml'),(6,6,'al gusto');
-- 7 Ensalada de pasta
INSERT INTO recipe_ingredients VALUES (7,13,'300g'),(7,10,'2 tomates'),(7,14,'100g'),(7,7,'3 cucharadas'),(7,6,'al gusto');
-- 8 Tostas de salmón
INSERT INTO recipe_ingredients VALUES (8,16,'1 unidad'),(8,14,'50g'),(8,7,'1 cucharada'),(8,6,'al gusto');
-- 9 Boquerones en vinagre
INSERT INTO recipe_ingredients VALUES (9,8,'3 dientes'),(9,7,'4 cucharadas'),(9,6,'al gusto');
-- 10 Crema de champiñones
INSERT INTO recipe_ingredients VALUES (10,9,'1 unidad'),(10,4,'30g'),(10,15,'200ml'),(10,6,'al gusto');
-- 11 Paella valenciana
INSERT INTO recipe_ingredients VALUES (11,12,'400g'),(11,11,'500g'),(11,19,'1 unidad'),(11,10,'2 tomates'),(11,7,'4 cucharadas'),(11,6,'al gusto');
-- 12 Lasaña boloñesa
INSERT INTO recipe_ingredients VALUES (12,13,'12 láminas'),(12,9,'1 unidad'),(12,10,'400g'),(12,14,'150g'),(12,15,'200ml'),(12,7,'2 cucharadas'),(12,6,'al gusto');
-- 13 Merluza en salsa verde
INSERT INTO recipe_ingredients VALUES (13,8,'3 dientes'),(13,7,'4 cucharadas'),(13,6,'al gusto');
-- 14 Carne guisada
INSERT INTO recipe_ingredients VALUES (14,9,'1 unidad'),(14,10,'2 tomates'),(14,20,'3 patatas'),(14,8,'3 dientes'),(14,7,'3 cucharadas'),(14,6,'al gusto');
-- 15 Risotto de setas
INSERT INTO recipe_ingredients VALUES (15,12,'300g'),(15,9,'1 unidad'),(15,4,'50g'),(15,14,'80g'),(15,6,'al gusto');
-- 16 Bacalao al pil pil
INSERT INTO recipe_ingredients VALUES (16,8,'4 dientes'),(16,7,'200ml'),(16,6,'al gusto');
-- 17 Fabada asturiana
INSERT INTO recipe_ingredients VALUES (17,9,'1 unidad'),(17,8,'3 dientes'),(17,7,'3 cucharadas'),(17,6,'al gusto');
-- 18 Pollo al curry
INSERT INTO recipe_ingredients VALUES (18,11,'800g'),(18,9,'1 unidad'),(18,15,'200ml'),(18,8,'2 dientes'),(18,6,'al gusto');
-- 19 Espaguetis boloñesa
INSERT INTO recipe_ingredients VALUES (19,13,'400g'),(19,9,'1 unidad'),(19,10,'400g'),(19,8,'2 dientes'),(19,7,'2 cucharadas'),(19,6,'al gusto');
-- 20 Arroz tres delicias
INSERT INTO recipe_ingredients VALUES (20,12,'300g'),(20,2,'3 unidades'),(20,9,'1 unidad'),(20,7,'2 cucharadas'),(20,6,'al gusto');
-- 21 Tiramisú
INSERT INTO recipe_ingredients VALUES (21,2,'4 unidades'),(21,5,'100g'),(21,15,'250ml'),(21,17,'50g');
-- 22 Tarta de queso
INSERT INTO recipe_ingredients VALUES (22,14,'500g'),(22,2,'3 unidades'),(22,5,'150g'),(22,15,'200ml'),(22,16,'1 unidad');
-- 23 Crema catalana
INSERT INTO recipe_ingredients VALUES (23,3,'500ml'),(23,2,'4 unidades'),(23,5,'120g'),(23,16,'1 unidad');
-- 24 Bizcocho de limón
INSERT INTO recipe_ingredients VALUES (24,1,'200g'),(24,5,'180g'),(24,2,'3 unidades'),(24,16,'2 unidades'),(24,18,'1 sobre'),(24,4,'100g');
-- 25 Mousse de chocolate
INSERT INTO recipe_ingredients VALUES (25,17,'200g'),(25,2,'4 unidades'),(25,5,'50g'),(25,15,'200ml');
-- 26 Arroz con leche
INSERT INTO recipe_ingredients VALUES (26,12,'200g'),(26,3,'1 litro'),(26,5,'150g'),(26,16,'1 unidad');
-- 27 Tarta de manzana
INSERT INTO recipe_ingredients VALUES (27,1,'200g'),(27,4,'100g'),(27,5,'150g'),(27,2,'2 unidades'),(27,18,'1 sobre');
-- 28 Coulant de chocolate
INSERT INTO recipe_ingredients VALUES (28,17,'150g'),(28,4,'100g'),(28,5,'80g'),(28,2,'4 unidades'),(28,1,'30g');
-- 29 Panna cotta
INSERT INTO recipe_ingredients VALUES (29,15,'500ml'),(29,5,'80g'),(29,16,'1 unidad');
-- 30 Magdalenas caseras
INSERT INTO recipe_ingredients VALUES (30,1,'200g'),(30,5,'150g'),(30,2,'3 unidades'),(30,3,'100ml'),(30,7,'80ml'),(30,18,'1 sobre');

-- 1 Hummus casero
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (1, 1, 'Escurre y enjuaga los garbanzos cocidos.'),
                                                                  (1, 2, 'Tritura los garbanzos junto con el ajo, el zumo de limón y el aceite de oliva hasta obtener una pasta suave.'),
                                                                  (1, 3, 'Ajusta de sal al gusto y sirve con un chorrito de aceite por encima.');

-- 2 Patatas bravas
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (2, 1, 'Pela y corta las patatas en cubos de tamaño similar.'),
                                                                  (2, 2, 'Fríe las patatas en aceite caliente hasta que estén doradas y crujientes. Escurre sobre papel absorbente.'),
                                                                  (2, 3, 'Prepara la salsa brava sofriendo el ajo y el tomate con un poco de aceite. Tritura y sazona.'),
                                                                  (2, 4, 'Vierte la salsa sobre las patatas y sirve inmediatamente.');

-- 3 Sopa de cebolla
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (3, 1, 'Corta las cebollas en juliana fina.'),
                                                                  (3, 2, 'Pocha las cebollas en mantequilla a fuego lento durante 20 minutos hasta que estén caramelizadas.'),
                                                                  (3, 3, 'Añade caldo y cocina 15 minutos más a fuego medio.'),
                                                                  (3, 4, 'Sirve en cazuela con queso rallado por encima y gratina en el horno 5 minutos.');

-- 4 Aguacate con gambas
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (4, 1, 'Corta el aguacate por la mitad y retira el hueso.'),
                                                                  (4, 2, 'Aliña con zumo de limón, aceite de oliva y sal.'),
                                                                  (4, 3, 'Coloca las gambas cocidas sobre el aguacate y sirve frío.');

-- 5 Pimientos rellenos
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (5, 1, 'Corta la tapa de los pimientos y retira las semillas.'),
                                                                  (5, 2, 'Sofríe la cebolla picada con aceite hasta que esté transparente. Añade el tomate y cocina 10 minutos.'),
                                                                  (5, 3, 'Mezcla el sofrito con el arroz cocido y rellena los pimientos.'),
                                                                  (5, 4, 'Hornea los pimientos a 180°C durante 25 minutos hasta que estén tiernos.');

-- 6 Crema de calabaza
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (6, 1, 'Pela y trocea la calabaza en cubos. Pica la cebolla.'),
                                                                  (6, 2, 'Sofríe la cebolla en mantequilla, añade la calabaza y cubre con caldo. Cocina 20 minutos.'),
                                                                  (6, 3, 'Tritura todo hasta obtener una crema fina. Añade la nata y ajusta de sal.'),
                                                                  (6, 4, 'Sirve caliente con un chorrito de nata por encima.');

-- 7 Ensalada de pasta
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (7, 1, 'Cuece la pasta en agua con sal según las instrucciones del paquete. Escurre y deja enfriar.'),
                                                                  (7, 2, 'Corta los tomates en dados y el queso en cubos.'),
                                                                  (7, 3, 'Mezcla la pasta con los tomates, el queso y aliña con aceite de oliva y sal.');

-- 8 Tostas de salmón
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (8, 1, 'Tuesta el pan en el horno o tostadora hasta que esté crujiente.'),
                                                                  (8, 2, 'Extiende queso crema sobre cada tosta.'),
                                                                  (8, 3, 'Coloca lonchas de salmón encima, añade unas gotas de limón y sirve.');

-- 9 Boquerones en vinagre
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (9, 1, 'Limpia los boquerones, retira la espina central y ábrelos en dos filetes.'),
                                                                  (9, 2, 'Cúbrelos con aceite de oliva y vinagre a partes iguales. Deja marinar en nevera al menos 4 horas.'),
                                                                  (9, 3, 'Escurre, añade ajo laminado y sal. Sirve fríos.');

-- 10 Crema de champiñones
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (10, 1, 'Limpia y lamina los champiñones. Pica la cebolla.'),
                                                                  (10, 2, 'Sofríe la cebolla en mantequilla, añade los champiñones y cocina hasta que suelten el agua.'),
                                                                  (10, 3, 'Añade la nata, cocina 5 minutos más y tritura hasta obtener una crema suave. Ajusta de sal.');

-- 11 Paella valenciana
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (11, 1, 'Sofríe el pollo troceado en aceite hasta dorar. Reserva.'),
                                                                  (11, 2, 'En el mismo aceite sofríe el pimiento y el tomate rallado durante 10 minutos.'),
                                                                  (11, 3, 'Añade el arroz y sofríe 2 minutos. Incorpora el caldo caliente en proporción 2:1.'),
                                                                  (11, 4, 'Cocina a fuego fuerte 10 minutos y a fuego lento otros 8. Deja reposar 5 minutos antes de servir.');

-- 12 Lasaña boloñesa
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (12, 1, 'Sofríe la cebolla picada con aceite. Añade el tomate y cocina 15 minutos a fuego medio.'),
                                                                  (12, 2, 'Prepara la bechamel con mantequilla, harina y leche. Sazona.'),
                                                                  (12, 3, 'Monta la lasaña alternando capas de láminas, boloñesa y bechamel. Termina con bechamel y queso.'),
                                                                  (12, 4, 'Hornea a 180°C durante 30 minutos hasta que la superficie esté dorada.');

-- 13 Merluza en salsa verde
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (13, 1, 'Dora el ajo laminado en aceite de oliva a fuego medio.'),
                                                                  (13, 2, 'Añade los lomos de merluza y cocina 3 minutos por cada lado.'),
                                                                  (13, 3, 'Agrega perejil picado y un poco de caldo de pescado. Mueve la cazuela en círculos para ligar la salsa. Sirve caliente.');

-- 14 Carne guisada
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (14, 1, 'Corta la carne en trozos y sella en aceite caliente hasta dorar por todos los lados.'),
                                                                  (14, 2, 'Sofríe la cebolla y el ajo picados. Añade el tomate y cocina 10 minutos.'),
                                                                  (14, 3, 'Incorpora la carne, las patatas troceadas y cubre con agua o caldo. Cocina a fuego lento 60 minutos.'),
                                                                  (14, 4, 'Ajusta de sal y sirve caliente.');

-- 15 Risotto de setas
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (15, 1, 'Sofríe la cebolla picada en mantequilla hasta que esté transparente.'),
                                                                  (15, 2, 'Añade el arroz y sofríe 2 minutos. Incorpora las setas limpias y troceadas.'),
                                                                  (15, 3, 'Añade caldo caliente poco a poco, removiendo constantemente durante 18 minutos.'),
                                                                  (15, 4, 'Retira del fuego, añade queso rallado y mantequilla. Remueve y sirve.');

-- 16 Bacalao al pil pil
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (16, 1, 'Desala el bacalao 48 horas cambiando el agua cada 8 horas. Seca bien los lomos.'),
                                                                  (16, 2, 'Confita el ajo laminado en aceite de oliva a fuego muy bajo. Retira el ajo y reserva.'),
                                                                  (16, 3, 'En el mismo aceite templado, coloca el bacalao con la piel hacia arriba. Mueve la cazuela constantemente en círculos para que la gelatina del bacalao emulsione con el aceite.'),
                                                                  (16, 4, 'Cuando la salsa esté ligada y cremosa, añade el ajo reservado y sirve.');

-- 17 Fabada asturiana
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (17, 1, 'Pon las fabes a remojo la noche anterior.'),
                                                                  (17, 2, 'Coloca las fabes en una olla con agua fría junto con los embutidos y el ajo. Lleva a ebullición.'),
                                                                  (17, 3, 'Cocina a fuego muy lento durante 2 horas, añadiendo agua fría de vez en cuando para cortar la cocción.'),
                                                                  (17, 4, 'Ajusta de sal al final y sirve caliente.');

-- 18 Pollo al curry
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (18, 1, 'Corta el pollo en dados y sella en aceite hasta dorar.'),
                                                                  (18, 2, 'Sofríe la cebolla y el ajo picados. Añade el curry en polvo y rehoga 1 minuto.'),
                                                                  (18, 3, 'Incorpora el pollo y la nata. Cocina a fuego medio 15 minutos hasta que la salsa espese.'),
                                                                  (18, 4, 'Ajusta de sal y sirve acompañado de arroz blanco.');

-- 19 Espaguetis boloñesa
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (19, 1, 'Sofríe la cebolla y el ajo picados en aceite. Añade la carne picada y cocina hasta dorar.'),
                                                                  (19, 2, 'Incorpora el tomate triturado y cocina a fuego lento 30 minutos. Ajusta de sal.'),
                                                                  (19, 3, 'Cuece los espaguetis en agua con sal según el paquete. Escurre.'),
                                                                  (19, 4, 'Mezcla la pasta con la salsa y sirve con queso rallado por encima.');

-- 20 Arroz tres delicias
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (20, 1, 'Cuece el arroz con el doble de agua y sal. Deja enfriar completamente.'),
                                                                  (20, 2, 'Bate los huevos y cuaja una tortilla fina. Córtala en tiras pequeñas.'),
                                                                  (20, 3, 'Saltea la cebolla picada en aceite, añade el arroz frío y rehoga a fuego fuerte.'),
                                                                  (20, 4, 'Incorpora los huevos y la salsa de soja. Mezcla bien y sirve caliente.');

-- 21 Tiramisú
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (21, 1, 'Separa las yemas de las claras. Bate las yemas con el azúcar hasta blanquear.'),
                                                                  (21, 2, 'Mezcla el mascarpone con las yemas hasta obtener una crema homogénea.'),
                                                                  (21, 3, 'Moja los bizcochos en café frío y coloca una capa en el molde. Cubre con crema de mascarpone.'),
                                                                  (21, 4, 'Repite las capas y termina con crema. Espolvorea cacao en polvo y refrigera 4 horas antes de servir.');

-- 22 Tarta de queso
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (22, 1, 'Precalienta el horno a 200°C. Forra un molde con papel de hornear.'),
                                                                  (22, 2, 'Bate el queso crema con el azúcar, los huevos, la nata y el zumo de limón hasta obtener una mezcla suave.'),
                                                                  (22, 3, 'Vierte en el molde y hornea 30 minutos hasta que la superficie esté dorada pero el centro tiemble ligeramente.'),
                                                                  (22, 4, 'Deja enfriar completamente antes de desmoldar. Refrigera al menos 2 horas antes de servir.');

-- 23 Crema catalana
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (23, 1, 'Calienta la leche con la piel de limón a fuego medio sin que llegue a hervir.'),
                                                                  (23, 2, 'Bate las yemas con el azúcar. Añade la leche caliente poco a poco sin dejar de remover.'),
                                                                  (23, 3, 'Devuelve la mezcla al fuego y cocina removiendo hasta que espese. Vierte en cazuelitas.'),
                                                                  (23, 4, 'Refrigera al menos 2 horas. Espolvorea azúcar y quema con soplete justo antes de servir.');

-- 24 Bizcocho de limón
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (24, 1, 'Precalienta el horno a 180°C. Engrasa un molde con mantequilla.'),
                                                                  (24, 2, 'Bate los huevos con el azúcar hasta que doblen su volumen. Añade la ralladura y el zumo de limón.'),
                                                                  (24, 3, 'Incorpora la harina tamizada con la levadura y la mantequilla derretida. Mezcla con movimientos envolventes.'),
                                                                  (24, 4, 'Vierte en el molde y hornea 40 minutos. Comprueba con un palillo antes de sacar.');

-- 25 Mousse de chocolate
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (25, 1, 'Derrite el chocolate al baño maría. Deja templar.'),
                                                                  (25, 2, 'Separa yemas y claras. Bate las yemas con el azúcar e incorpora al chocolate.'),
                                                                  (25, 3, 'Monta la nata a punto firme y las claras a punto de nieve. Incorpora primero la nata y luego las claras al chocolate con movimientos envolventes.'),
                                                                  (25, 4, 'Reparte en copas y refrigera al menos 3 horas antes de servir.');

-- 26 Arroz con leche
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (26, 1, 'Lleva la leche a ebullición con la piel de limón a fuego medio.'),
                                                                  (26, 2, 'Añade el arroz y cocina a fuego lento removiendo frecuentemente durante 30 minutos.'),
                                                                  (26, 3, 'Incorpora el azúcar y cocina 10 minutos más hasta que espese.'),
                                                                  (26, 4, 'Sirve en cuencos y espolvorea canela por encima. Puede servirse caliente o frío.');

-- 27 Tarta de manzana
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (27, 1, 'Prepara la masa mezclando harina, mantequilla fría en cubos, azúcar y huevo. Refrigera 30 minutos.'),
                                                                  (27, 2, 'Pela y lamina las manzanas finamente.'),
                                                                  (27, 3, 'Extiende la masa en el molde, coloca las manzanas en forma de abanico y espolvorea azúcar.'),
                                                                  (27, 4, 'Hornea a 180°C durante 35 minutos hasta que la masa esté dorada y las manzanas tiernas.');

-- 28 Coulant de chocolate
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (28, 1, 'Precalienta el horno a 200°C. Engrasa y enharina moldes individuales.'),
                                                                  (28, 2, 'Derrite el chocolate con la mantequilla al baño maría. Deja templar.'),
                                                                  (28, 3, 'Bate los huevos con el azúcar. Añade el chocolate y la harina. Mezcla hasta integrar.'),
                                                                  (28, 4, 'Vierte en los moldes y hornea exactamente 8 minutos. El centro debe quedar líquido. Desmolda y sirve inmediatamente.');

-- 29 Panna cotta
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (29, 1, 'Hidrata la gelatina en agua fría durante 5 minutos.'),
                                                                  (29, 2, 'Calienta la nata con el azúcar y la vaina de vainilla hasta que casi hierva. Retira la vainilla.'),
                                                                  (29, 3, 'Escurre la gelatina e incorpora a la nata caliente. Remueve hasta disolver completamente.'),
                                                                  (29, 4, 'Vierte en moldes y refrigera al menos 4 horas. Desmolda y sirve con coulis de frutos rojos.');

-- 30 Magdalenas caseras
INSERT INTO recipe_steps (recipe_id, step_order, description) VALUES
                                                                  (30, 1, 'Precalienta el horno a 180°C. Prepara los moldes de magdalena.'),
                                                                  (30, 2, 'Bate los huevos con el azúcar hasta blanquear. Añade la leche y el aceite.'),
                                                                  (30, 3, 'Incorpora la harina tamizada con la levadura. Mezcla hasta obtener una masa homogénea.'),
                                                                  (30, 4, 'Rellena los moldes hasta 3/4 de su capacidad y hornea 20 minutos hasta que estén doradas.');

