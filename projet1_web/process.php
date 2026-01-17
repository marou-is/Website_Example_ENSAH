<?php
// Include database configuration
require_once 'config.php';

// Start session for storing messages
session_start();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Initialize errors array
    $errors = [];
    
    // Sanitize and validate input data
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $sexe = htmlspecialchars($_POST['sexe'] ?? '');
    $age = filter_var($_POST['age'] ?? 0, FILTER_VALIDATE_INT);
    $naissance = htmlspecialchars($_POST['naissance'] ?? '');
    $couleur = htmlspecialchars($_POST['couleur'] ?? '');
    $pays = htmlspecialchars($_POST['pays'] ?? '');
    $height = filter_var($_POST['height'] ?? 0, FILTER_VALIDATE_INT);
    $salary = filter_var($_POST['salary'] ?? 0, FILTER_VALIDATE_INT);
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $mobile = htmlspecialchars(trim($_POST['mobile'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $coordonnees = $_POST['coordonnees'] ?? [];
    
    // Validate required fields
    if (empty($nom)) {
        $errors[] = "Le nom est requis.";
    }
    
    if (!$age || $age < 1 || $age > 120) {
        $errors[] = "L'âge doit être entre 1 et 120.";
    }
    
    if (empty($naissance)) {
        $errors[] = "La date de naissance est requise.";
    }
    
    if (empty($pays)) {
        $errors[] = "Le pays est requis.";
    }
    
    if (!$email) {
        $errors[] = "Une adresse email valide est requise.";
    }
    
    if (empty($mobile)) {
        $errors[] = "Le numéro de mobile est requis.";
    }
    
    if (empty($address)) {
        $errors[] = "L'adresse est requise.";
    }
    
    if (empty($coordonnees)) {
        $errors[] = "Veuillez sélectionner au moins une méthode de contact.";
    }
    
    // Handle file upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = $_FILES['photo']['type'];
        $filesize = $_FILES['photo']['size'];
        
        // Get file extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate file
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format de fichier non autorisé. Utilisez JPG, JPEG, PNG ou GIF.";
        }
        
        // Check file size (5MB max)
        if ($filesize > 5 * 1024 * 1024) {
            $errors[] = "Le fichier est trop grand. Taille maximale: 5MB.";
        }
        
        // If no errors, upload file
        if (empty($errors)) {
            $uploadDir = 'uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $newFilename = uniqid() . '_' . time() . '.' . $ext;
            $uploadPath = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                $photoPath = $uploadPath;
            } else {
                $errors[] = "Erreur lors du téléchargement de la photo.";
            }
        }
    }
    
    // If there are errors, display them
    if (!empty($errors)) {
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Erreur - Formulaire</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo "h1 { color: #f44336; }";
        echo ".errors { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }";
        echo ".errors ul { margin: 10px 0; padding-left: 20px; }";
        echo ".btn { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }";
        echo ".btn:hover { background: #0b7dda; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<h1>Erreurs dans le formulaire</h1>";
        echo "<div class='errors'>";
        echo "<strong>Veuillez corriger les erreurs suivantes:</strong>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
        echo "</div>";
        echo "<a href='javascript:history.back()' class='btn'>Retour au formulaire</a>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
    }
    
    // Convert coordonnees array to string
    $coordonneesString = implode(', ', $coordonnees);
    
    // Connect to database
    $conn = getDBConnection();
    
    // Check if email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        closeDBConnection($conn);
        
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Erreur - Email déjà utilisé</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo "h1 { color: #f44336; }";
        echo ".error { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }";
        echo ".btn { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<h1>Email déjà utilisé</h1>";
        echo "<div class='error'>";
        echo "<strong>Cet email est déjà enregistré dans notre système.</strong><br>";
        echo "Veuillez utiliser une autre adresse email.";
        echo "</div>";
        echo "<a href='javascript:history.back()' class='btn'>Retour au formulaire</a>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
    }
    $stmt->close();
    
    // Prepare SQL insert statement
    $insertQuery = "INSERT INTO users (nom, sexe, age, date_naissance, couleur_preferee, pays, height, salary, email, mobile, address, coordonnees, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertQuery);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("ssisssiisssss", $nom, $sexe, $age, $naissance, $couleur, $pays, $height, $salary, $email, $mobile, $address, $coordonneesString, $photoPath);
    
    // Execute the query
    if ($stmt->execute()) {
        $insertedId = $conn->insert_id;
        $stmt->close();
        closeDBConnection($conn);
        
        // Display success page
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Inscription réussie</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #69b9e9;
                    min-height: 100vh;
                    padding: 20px;
                }
                .container {
                    max-width: 700px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                }
                h1 {
                    color: #4CAF50;
                    text-align: center;
                    margin-bottom: 30px;
                }
                .success-message {
                    background: #e8f5e9;
                    border-left: 4px solid #4CAF50;
                    padding: 15px;
                    margin-bottom: 30px;
                }
                .data-section {
                    background: #f5f5f5;
                    padding: 20px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                }
                .data-section h2 {
                    color: #333;
                    font-size: 18px;
                    margin-bottom: 15px;
                    border-bottom: 2px solid #ddd;
                    padding-bottom: 10px;
                }
                .data-item {
                    display: flex;
                    margin-bottom: 10px;
                    padding: 8px;
                    background: white;
                    border-radius: 4px;
                }
                .data-label {
                    font-weight: bold;
                    min-width: 180px;
                    color: #555;
                }
                .data-value {
                    color: #333;
                }
                .photo-preview {
                    max-width: 200px;
                    border-radius: 4px;
                    margin-top: 10px;
                }
                .color-box {
                    display: inline-block;
                    width: 30px;
                    height: 30px;
                    border-radius: 4px;
                    border: 1px solid #ccc;
                    vertical-align: middle;
                    margin-left: 10px;
                }
                .btn-home {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #2196F3;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    text-align: center;
                    margin-top: 20px;
                }
                .btn-home:hover {
                    background: #0b7dda;
                }
                .btn-view {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    text-align: center;
                    margin-top: 20px;
                    margin-left: 10px;
                }
                .btn-view:hover {
                    background: #45a049;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>✓ Inscription réussie!</h1>
                
                <div class="success-message">
                    <strong>Merci <?php echo $nom; ?>!</strong> Vos informations ont été enregistrées dans la base de données.<br>
                    Votre numéro d'inscription est: <strong>#<?php echo $insertedId; ?></strong>
                </div>
                
                <div class="data-section">
                    <h2>Photo</h2>
                    <?php if ($photoPath): ?>
                        <img src="<?php echo $photoPath; ?>" alt="Photo" class="photo-preview">
                    <?php else: ?>
                        <p><em>Aucune photo téléchargée</em></p>
                    <?php endif; ?>
                </div>
                
                <div class="data-section">
                    <h2>Informations générales</h2>
                    <div class="data-item">
                        <span class="data-label">Nom:</span>
                        <span class="data-value"><?php echo $nom; ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Sexe:</span>
                        <span class="data-value"><?php echo $sexe; ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Age:</span>
                        <span class="data-value"><?php echo $age; ?> ans</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Date de naissance:</span>
                        <span class="data-value"><?php echo date('d/m/Y', strtotime($naissance)); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Couleur préférée:</span>
                        <span class="data-value">
                            <?php echo $couleur; ?>
                            <span class="color-box" style="background-color: <?php echo $couleur; ?>;"></span>
                        </span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Pays:</span>
                        <span class="data-value"><?php echo $pays; ?></span>
                    </div>
                </div>
                
                <div class="data-section">
                    <h2>Indicateurs</h2>
                    <div class="data-item">
                        <span class="data-label">Taille:</span>
                        <span class="data-value"><?php echo $height; ?> cm</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Salaire:</span>
                        <span class="data-value"><?php echo number_format($salary, 0, ',', ' '); ?> €</span>
                    </div>
                </div>
                
                <div class="data-section">
                    <h2>Coordonnées</h2>
                    <div class="data-item">
                        <span class="data-label">Email:</span>
                        <span class="data-value"><?php echo $email; ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Mobile:</span>
                        <span class="data-value"><?php echo $mobile; ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Adresse:</span>
                        <span class="data-value"><?php echo nl2br($address); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Méthodes de contact:</span>
                        <span class="data-value"><?php echo $coordonneesString; ?></span>
                    </div>
                </div>
                
                <center>
                    <a href="inscription.html" class="btn-home">Nouvelle inscription</a>
                    <a href="view_users.php" class="btn-view">Voir tous les utilisateurs</a>
                </center>
            </div>
        </body>
        </html>
        <?php
        
    } else {
        // Error executing query
        $stmt->close();
        closeDBConnection($conn);
        
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<title>Erreur Base de données</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }";
        echo "h1 { color: #f44336; }";
        echo ".error { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<h1>Erreur lors de l'enregistrement</h1>";
        echo "<div class='error'>";
        echo "Une erreur s'est produite lors de l'enregistrement des données. Veuillez réessayer.";
        echo "</div>";
        echo "<a href='javascript:history.back()'>Retour</a>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    }
    
} else {
    // If accessed directly without POST, redirect to form
    header('Location: form.html');
    exit;
}
?>