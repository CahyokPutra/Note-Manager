<?php
session_start();

class Note {
    public $title;
    public $content;

    public function __construct($title, $content) {
        $this->title = $title;
        $this->content = $content;
    }
}

class NoteManager {
    private $notes = [];

    public function __construct() {
        if (isset($_SESSION['notes'])) {
            $this->notes = $_SESSION['notes'];
        }
    }

    public function addNote() {
        $title = $_POST['title'];
        $content = $_POST['content'];

        $note = new Note($title, $content);
        $this->notes[] = $note;
        $_SESSION['notes'] = $this->notes;
    }

    public function editNote() {
        $index = $_POST['index'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        if (isset($this->notes[$index])) {
            $this->notes[$index]->title = $title;
            $this->notes[$index]->content = $content;
            $_SESSION['notes'] = $this->notes;
        }
    }

    public function deleteNote() {
        $index = $_POST['index'];
        if (isset($this->notes[$index])) {
            unset($this->notes[$index]);
            $this->notes = array_values($this->notes); // Re-indexing
            $_SESSION['notes'] = $this->notes;
        }
    }

    public function viewNotes() {
        return $this->notes;
    }
}

$noteManager = new NoteManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $noteManager->addNote();
    } elseif (isset($_POST['edit'])) {
        $noteManager->editNote();
    } elseif (isset($_POST['delete'])) {
        $noteManager->deleteNote();
    }
}

$notes = $noteManager->viewNotes();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Manager</title>
    <link rel="stylesheet" href="styles.css"> <!-- Gaya CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        textarea {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #e9ecef;
            margin: 10px 0;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #editForm {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Note Manager</h1>

        <h2>Tambah Note</h2>
        <form method="post">
            <input type="text" name="title" placeholder="Judul" required>
            <textarea name="content" placeholder="Isi Note" required></textarea>
            <button type="submit" name="add">Tambah Note</button>
        </form>

        <h2>Daftar Note</h2>
        <ul>
            <?php foreach ($notes as $index => $note): ?>
                <li>
                    <strong><?php echo htmlspecialchars($note->title); ?></strong>: <?php echo htmlspecialchars($note->content); ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="index" value="<?php echo $index; ?>">
                        <button type="button" onclick="editNote(<?php echo $index; ?>)">Edit</button>
                        <button type="submit" name="delete">Hapus</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <div id="editForm">
            <h2>Edit Note</h2>
            <form method="post" id="formEdit">
                <input type="hidden" name="index" id="editIndex">
                <input type="text" name="title" id="editTitle" placeholder="Judul" required>
                <textarea name="content" id="editContent" placeholder="Isi Note" required></textarea>
                <button type="submit" name="edit">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        function editNote(index) {
            const notes = <?php echo json_encode($notes); ?>;
            document.getElementById('editIndex').value = index;
            document.getElementById('editTitle').value = notes[index].title;
            document.getElementById('editContent').value = notes[index].content;
            document.getElementById('editForm').style.display = 'block';
        }
    </script>
</body>
</html>
