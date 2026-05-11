<?php
// Save unknown questions safely

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['question']) && strlen(trim($data['question'])) >= 5) {
        $logEntry = date('Y-m-d H:i:s') . " | Lang: " . ($data['lang'] ?? 'unknown') . 
                    " | Question: " . trim($data['question']) . "\n";
        
        file_put_contents('unknown_questions.log', $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>