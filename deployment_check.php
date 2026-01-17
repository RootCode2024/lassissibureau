<?php

// Script de vérification basique à uploader sur LWS
// Nommez-le check.php et visitez-le via le navigateur

echo '<h1>Vérification Pré-requis Serveur (Lassissi Tech)</h1>';

// 1. Version PHP
echo '<h2>1. Version PHP</h2>';
$phpVersion = phpversion();
echo 'Version actuelle : <strong>'.$phpVersion.'</strong><br>';
if (version_compare($phpVersion, '8.2.0', '>=')) {
    echo "<span style='color:green'>✅ Compatible (>= 8.2)</span>";
} else {
    echo "<span style='color:red'>❌ Incompatible (Nécessite PHP 8.2+)</span>. Veuillez changer la version dans cPanel/LWS.";
}

// 2. Extensions Requises
echo '<h2>2. Extensions Requises</h2>';
$requiredExtensions = [
    'ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'mbstring',
    'openssl', 'pcre', 'pdo', 'session', 'tokenizer', 'xml',
    'pdo_mysql', // (ou pdo_pgsql si vous utilisez Postgres sur LWS)
];

echo '<ul>';
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li><span style='color:green'>✅ $ext</span></li>";
    } else {
        echo "<li><span style='color:red'>❌ $ext (Manquant)</span></li>";
    }
}
echo '</ul>';

// 3. Dossier
echo '<h2>3. Document Root</h2>';
echo 'Dossier actuel : '.__DIR__.'<br>';
echo '<em>Vérifiez que ce dossier correspond bien à celui de votre sous-domaine.</em>';
