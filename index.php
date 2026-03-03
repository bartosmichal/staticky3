<?php
// Načtení dat ze souboru profile.json
$json_file = 'profile.json';
$json_data = file_get_contents($json_file);
$profile = json_decode($json_data, true);

$message = "";
$messageType = "";

// Zpracování POST požadavku pro přidání nového zájmu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_interest"])) {
    $new_interest = trim($_POST["new_interest"]);

    if (empty($new_interest)) {
        $message = "Pole nesmí být prázdné.";
        $messageType = "error";
    } else {
        $interests = $profile['interests'] ?? [];
        $exists = false;

        // Kontrola duplicity (bez ohledu na velikost písmen)
        foreach ($interests as $interest) {
            if (strtolower($interest) === strtolower($new_interest)) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $message = "Tento zájem už existuje.";
            $messageType = "error";
        } else {
            // Přidání nového zájmu a uložení
            $profile['interests'][] = $new_interest;
            if (file_put_contents($json_file, json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $message = "Zájem byl úspěšně přidán.";
                $messageType = "success";
            } else {
                $message = "Chyba při ukládání do souboru.";
                $messageType = "error";
            }
        }
    }
}

// Základní proměnné s ošetřením proti XSS
$name = htmlspecialchars($profile['name'] ?? 'Jméno nenalezeno');
$skills = $profile['skills'] ?? [];
$interests = $profile['interests'] ?? [];
$projects = $profile['projects'] ?? [];
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name; ?> | IT Profil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="glass-bg"></div>

    <main class="container">
        <header class="profile-header">
            <div class="avatar-container">
                <img src="avatar.png" alt="Profile" id="profile-img">
            </div>
            <h1><?php echo $name; ?></h1>
            <p class="subtitle">IT Student & Developer</p>
        </header>

        <section class="content-grid">
            <div class="card skills-card">
                <h2><span class="icon">🚀</span> Dovednosti</h2>
                <ul id="skills">
                    <?php foreach ($skills as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card interests-card">
                <h2><span class="icon">🎨</span> Zájmy</h2>
                
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>

                <div id="interests">
                    <ul>
                        <?php foreach ($interests as $interest): ?>
                            <li><?php echo htmlspecialchars($interest); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <form method="POST" class="add-interest-form">
                    <input type="text" name="new_interest" placeholder="Nový zájem..." required>
                    <button type="submit">Přidat zájem</button>
                </form>
            </div>
        </section>

        <?php if (!empty($projects)): ?>
        <section class="projects-section">
            <h2><span class="icon">💻</span> Projekty</h2>
            <div id="projects" class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p><?php echo htmlspecialchars($project['description']); ?></p>
                        <span class="tech-tag"><?php echo htmlspecialchars($project['tech']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>
</body>

</html>
