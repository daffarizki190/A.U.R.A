<?php
file_put_contents('hash_final_fix.txt', password_hash('password123', PASSWORD_BCRYPT));
echo "Hash created in hash_final_fix.txt\n";
