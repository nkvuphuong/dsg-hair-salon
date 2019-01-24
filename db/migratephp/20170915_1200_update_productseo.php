<?php

\core\ezy::$app_dir = "web"; // Alway required this line if you want to load models in /web/
\core\ezy::load_model("product");

\models\product::update_sql();