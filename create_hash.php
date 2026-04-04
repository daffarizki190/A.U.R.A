<?php
file_put_contents('hash_final.txt', password_hash('password123', PASSWORD_BCRYPT));
echo "Hash created in hash_final.txt\n";
