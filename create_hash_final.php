<?php
file_put_contents(__DIR__ . '/hash_final_final.txt', password_hash('password123', PASSWORD_BCRYPT));
echo "Hash created in " . __DIR__ . "/hash_final_final.txt\n";
