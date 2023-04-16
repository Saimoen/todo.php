<?php
const ERROR_REQUIRED = 'Veuillez renseigner une todo';
const ERROR_TOO_SHORT = 'Veuillez entrer au moins 5 caractères';

$filename = __DIR__ . "/data/todos.json";
$error = '';
$todo = '';
$todos = [];

/** Vérifie la data au format JSON **/
if (file_exists($filename)) {
    $data = file_get_contents($filename);
    $todos = json_decode($data, true) ?? [];
}


/** Vérifie qu'il s'agit d'un form de type POST **/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /** Désinfecte (sanitize) l'input des failles XSS **/
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $todo = $_POST['todo'] ?? '';

    /** Vérifie les erreurs des inputs **/
    if (!$todo) {
        $error = ERROR_REQUIRED;
    } elseif (mb_strlen($todo)  < 5) {
        $error = ERROR_TOO_SHORT;
    }

    /** Ajoute une todo si y'a pas d'erreurs **/
    if (!$error) {
        $todos = [...$todos, [
            'name' => $todo,
            'done' => false,
            'id' => time()
        ]];
        /** Ecris la todo dans le fichier JSON **/
        file_put_contents($filename, json_encode($todos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        header('Location: /');
    }
}

?>



<!DOCTYPE html>
<html>
<?php require_once 'includes/head.php' ?>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="todo-container">
                <h1>Ma ToDo</h1>
                <form action="/" method="post" class="todo-form">
                    <input value="<?= $todo  ?>" type="text" name="todo">
                    <button class="btn btn-primary">Ajouter</button>
                </form>
                <?php if ($error) :  ?>
                    <p class="text-danger"><?= $error  ?></p>
                <?php endif ?>
                <ul class="todo-list">
                    <?php foreach ($todos as $t) :  ?>
                        <li class="todo-item <?= $t['done'] ? 'low-opacity' : '' ?>">
                            <span class="todo-name"><?= $t['name'] ?></span>
                            <a href="/edit-todo.php?id=<?= $t['id'] ?>">
                                <button class="btn btn-primary btn-small"><?= $t['done'] ? 'Annuler' : 'Valider' ?></button>
                            </a>
                            <a href="/remove-todo.php?id=<?= $t['id'] ?>">
                                <button class="btn btn-danger btn-small">Supprimer</button>
                            </a>
                        </li>
                    <?php endforeach;  ?>
                </ul>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>