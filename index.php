<?php
session_start();

// --------------------
// Login хэсэг
// --------------------
$valid_user = 'admin';
$valid_pass = 'admin';

if(isset($_POST['login'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if($user === $valid_user && $pass === $valid_pass) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "⚠️ Username эсвэл password буруу!";
    }
}

// Logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// --------------------
// Ticket system хэсэг
// --------------------
if(!isset($_SESSION['tickets'])) $_SESSION['tickets'] = [];

$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket']) && isset($_SESSION['loggedin'])) {
    $ticket = [
        'id' => 'TKT-'.str_pad(count($_SESSION['tickets'])+1,5,'0',STR_PAD_LEFT),
        'title' => htmlspecialchars($_POST['title']),
        'email' => htmlspecialchars($_POST['email']),
        'priority' => htmlspecialchars($_POST['priority']),
        'description' => htmlspecialchars($_POST['description']),
        'date' => date('Y-m-d H:i:s'),
        'images' => []
    ];
    if(isset($_FILES['images'])) {
        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if($_FILES['images']['error'][$key] === 0) {
                $file_name = uniqid('img_').'_'.$_FILES['images']['name'][$key];
                $file_path = $upload_dir.$file_name;
                if(move_uploaded_file($tmp_name,$file_path)) $ticket['images'][] = $file_path;
            }
        }
    }
    $_SESSION['tickets'][] = $ticket;
    $success_message = "🎉 Тасалбар амжилттай үүсгэгдлээ! ID: ".$ticket['id'];
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎫 Modern Ticket System</title>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family:'Montserrat',sans-serif; background:linear-gradient(135deg,#1a2a6c,#b21f1f,#fdbb2d); background-size:400% 400%; animation:gradientBG 25s ease infinite; padding:20px; color:#fff;}
@keyframes gradientBG{0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}

/* --- Containers --- */
.container {max-width:1100px;margin:auto;}
h1{font-family:'Press Start 2P',cursive;font-size:3em;text-align:center;text-shadow:4px 4px 8px #000;margin-bottom:30px;text-transform:uppercase;}

/* --- Login Form --- */
.login-form{background:rgba(0,0,0,0.7);padding:35px;border-radius:20px;margin-bottom:30px;max-width:400px;margin:auto;box-shadow:0 0 25px rgba(255,255,255,0.2);}
.login-form h2{font-size:1.8em;margin-bottom:20px;text-align:center;color:#00ffe5;text-shadow:0 0 10px #00ffe5;}
.login-form input{width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:2px solid #00ffe5;background:rgba(255,255,255,0.05);color:#fff;font-weight:700; font-size:1.1em;transition:0.3s;}
.login-form input:focus{border-color:#ff00ff;box-shadow:0 0 15px #ff00ff;outline:none;}
.login-form .btn{background:linear-gradient(90deg,#ff00ff,#00ffe5);color:#fff;padding:15px;font-weight:900;border:none;border-radius:12px;width:100%;cursor:pointer;font-size:1em;text-transform:uppercase;transition:all 0.3s ease;}
.login-form .btn:hover{background:linear-gradient(90deg,#00ffe5,#ff00ff);color:#000;box-shadow:0 0 15px #ff00ff,0 0 20px #00ffe5;}

/* --- Ticket Form --- */
.ticket-form {background: rgba(0,0,0,0.75); padding:35px; border-radius:20px; margin-bottom:30px; box-shadow:0 0 25px rgba(255,255,255,0.2);}
.ticket-form h2{font-size:2em;margin-bottom:20px;text-align:center;color:#00ffe5;text-shadow:0 0 10px #00ffe5;}
input, textarea, select{width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:2px solid #00ffe5;background:rgba(255,255,255,0.05);color:#fff;font-weight:700;font-size:1.1em;transition:0.3s;}
input:focus,textarea:focus,select:focus{border-color:#ff00ff;box-shadow:0 0 15px #ff00ff;outline:none;}
.file-input-label{display:block;border:2px dashed #00ffe5;padding:12px;text-align:center;cursor:pointer;border-radius:12px;margin-bottom:10px;font-weight:900;transition:0.3s;color:#00ffe5;}
.file-input-label:hover{background:#00ffe5;color:#000;}
.btn{background:linear-gradient(90deg,#ff00ff,#00ffe5);color:#fff;padding:15px;font-weight:900;border:none;border-radius:12px;width:100%;cursor:pointer;font-size:1.1em;text-transform:uppercase;transition:all 0.3s ease;}
.btn:hover{background:linear-gradient(90deg,#00ffe5,#ff00ff);color:#000;box-shadow:0 0 15px #ff00ff,0 0 20px #00ffe5;}

/* --- Ticket Cards --- */
.ticket-card{background:rgba(0,0,0,0.6);padding:25px;border-radius:15px;margin-bottom:20px;box-shadow:0 0 15px #000;transition:all 0.4s;}
.ticket-card:hover{transform:translateY(-5px) rotateY(3deg);box-shadow:0 0 20px #00ffe5,0 0 25px #ff00ff inset;}
.ticket-card h3{font-size:1.8em;font-weight:900;color:#ffdd00;margin-bottom:10px;text-shadow:1px 1px #000;}
.ticket-card p{margin-bottom:8px;font-weight:600;line-height:1.4;}

/* Priority */
.priority-Бага{color:#00ff99;font-weight:900;}
.priority-Дунд{color:#ffd700;font-weight:900;}
.priority-Өндөр{color:#ff3b3b;font-weight:900;}

/* Ticket Images */
.ticket-images{display:grid;gap:10px;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));margin-top:10px;}
.ticket-images img{width:100%;height:120px;object-fit:cover;border-radius:12px;border:2px solid #00ffe5;transition:0.3s;cursor:pointer;}
.ticket-images img:hover{transform:scale(1.05);box-shadow:0 0 15px #ff00ff;}
.preview-container{display:grid;gap:10px;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));margin-top:10px;}
.preview-item{position:relative;}
.preview-item img{width:100%;height:120px;object-fit:cover;border-radius:12px;border:2px solid #ff00ff;}
.remove-image{position:absolute;top:5px;right:5px;width:28px;height:28px;border-radius:50%;background:#ff00ff;color:#fff;font-weight:900;border:none;cursor:pointer;}
@keyframes slideIn{0%{opacity:0;transform:translateY(-20px);}100%{opacity:1;transform:translateY(0);}}
.message{padding:15px;text-align:center;background:#00ffe5;color:#000;font-weight:900;border-radius:12px;margin-bottom:20px;text-shadow:1px 1px #000;animation:slideIn 0.5s ease-out;}
</style>
</head>
<body>

<?php if(!isset($_SESSION['loggedin'])): ?>
    <div class="login-form">
        <h2>🚀 Нэвтрэх</h2>
        <?php if(isset($login_error)): ?><p style="color:#ff4444;text-align:center;margin-bottom:15px;"><?=$login_error?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" name="login" type="submit">Нэвтрэх</button>
        </form>
    </div>
<?php else: ?>
<div class="container">
    <h1>🎫 Modern Ticket System</h1>
    <p style="text-align:right;margin-bottom:20px;"><a href="?logout=1" style="color:#ff00ff;font-weight:900;text-decoration:none;">🚪 Logout</a></p>

    <?php if(isset($success_message)): ?><div class="message"><?=$success_message?></div><?php endif; ?>

    <div class="ticket-form">
        <h2>Шинэ Тасалбар Үүсгэх</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>ГАРЧИГ *</label>
            <input type="text" name="title" placeholder="Тасалбарын гарчгийг оруулна уу" required>

            <label>ИМЭЙЛ *</label>
            <input type="email" name="email" placeholder="Таны имэйл" required>

            <label>АЧ ХОЛБОГДОЛ *</label>
            <select name="priority" required>
                <option value="Бага">Бага</option>
                <option value="Дунд" selected>Дунд</option>
                <option value="Өндөр">Өндөр</option>
            </select>

            <label>ТАЙЛБАР *</label>
            <textarea name="description" placeholder="Тайлбар бичнэ үү" required></textarea>

            <label>ЗУРГУУД ОРУУЛАХ</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple onchange="previewImages(event)">
            <label for="images" class="file-input-label">📷 Зураг сонгох</label>

            <div id="preview-container" class="preview-container"></div>

            <button class="btn" name="create_ticket" type="submit">ҮҮСГЭХ</button>
        </form>
    </div>

    <h2 style="color:#00ffe5;text-shadow:1px 1px #000;margin-bottom:15px;">ТАСАЛБАРУУДЫН ЖАГСААЛТ</h2>

    <?php if(empty($_SESSION['tickets'])): ?>
        <p style="text-align:center;border:2px dashed #00ffe5;padding:40px;font-weight:900;">Одоогоор тасалбар байхгүй</p>
    <?php else: ?>
        <?php foreach(array_reverse($_SESSION['tickets']) as $ticket): ?>
            <div class="ticket-card">
                <h3><?=$ticket['id']?></h3>
                <p><strong>ГАРЧИГ:</strong> <?=$ticket['title']?></p>
                <p><strong>ИМЭЙЛ:</strong> <?=$ticket['email']?></p>
                <p><strong>АЧ ХОЛБОГДОЛ:</strong> 
                    <span class="priority-<?=$ticket['priority']?>">
                        <?=$ticket['priority']=='Өндөр'?'🔴':($ticket['priority']=='Дунд'?'🟡':'🟢')?> <?=$ticket['priority']?>
                    </span>
                </p>
                <p><strong>ОГНОО:</strong> <?=$ticket['date']?></p>
                <p><strong>ТАЙЛБАР:</strong><br><?=nl2br($ticket['description'])?></p>

                <?php if(!empty($ticket['images'])): ?>
                    <p><strong>ХАВСАРГАСАН ЗУРАГ:</strong></p>
                    <div class="ticket-images">
                        <?php foreach($ticket['images'] as $img): ?>
                            <img src="<?=$img?>" onclick="window.open(this.src)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function previewImages(event){
    const files=event.target.files;
    const preview=document.getElementById("preview-container");
    preview.innerHTML="";
    for(let i=0;i<files.length;i++){
        const reader=new FileReader();
        reader.onload=e=>{
            const div=document.createElement("div");
            div.classList.add("preview-item");
            div.innerHTML=`<img src="${e.target.result}"><button class="remove-image" type="button">×</button>`;
            preview.appendChild(div);
            div.querySelector(".remove-image").onclick=()=>div.remove();
        }
        reader.readAsDataURL(files[i]);
    }
}
</script>
</body>
</html>
