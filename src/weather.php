<?php
/**
 * weather.php — Logique métier
 * Gère l'appel à l'API OpenWeatherMap et le traitement des données météo.
 */

/**
 * Récupère les données météo pour une ville donnée.
 * Retourne un tableau associatif avec 'temp', 'description', 'message', ou 'error'.
 *
 * @param string $city   Nom de la ville
 * @param string $apiKey Clé API OpenWeatherMap
 * @return array
 */
function getWeatherData(string $city, string $apiKey): array
{
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$city},MA&appid={$apiKey}&units=metric";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$response || $httpCode !== 200) {
        return ['error' => 'Impossible de contacter l\'API météo. Réessayez plus tard.'];
    }

    $data = json_decode($response, true);

    if (!isset($data['main'])) {
        return ['error' => 'Ville introuvable ou erreur API.'];
    }

    $temp        = $data['main']['temp'];
    $description = $data['weather'][0]['description'];

    // Message adapté selon la température
    if ($temp < 20) {
        $message = '⛈️ Mettez des vêtements chauds, il fait froid dehors ! 🧥';
    } else {
        $message = '☀️ Il fait chaud ! Vous pouvez porter des vêtements légers ! ☀️';
    }

    return [
        'city'        => $city,
        'temp'        => $temp,
        'description' => ucfirst($description),
        'message'     => $message,
    ];
}

// Traitement du formulaire
$weatherResult = null;
$selectedCity  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendww'])) {
    $selectedCity = htmlspecialchars(trim($_POST['city'] ?? ''));

    // Lecture de la clé API depuis variable d'environnement ou .env
    $apiKey = getenv('OPENWEATHER_API_KEY') ?: ($_ENV['OPENWEATHER_API_KEY'] ?? '');

    if (empty($apiKey)) {
        $weatherResult = ['error' => 'Clé API non configurée. Veuillez créer un fichier .env.'];
    } elseif (empty($selectedCity)) {
        $weatherResult = ['error' => 'Veuillez sélectionner une ville.'];
    } else {
        $weatherResult = getWeatherData($selectedCity, $apiKey);
    }
}
