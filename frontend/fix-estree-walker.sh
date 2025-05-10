#!/bin/bash

# Перевіряємо чи існує директорія
if [ -d "node_modules/estree-walker" ]; then
    echo "Виправляю package.json для estree-walker..."
    echo '{"type":"module","exports":{"import":"./src/index.js","require":"./dist/estree-walker.umd.js"}}' > node_modules/estree-walker/package.json
    echo "Виправлення завершено!"
else
    echo "Директорія node_modules/estree-walker не знайдена. Перевірте чи встановлені залежності."
fi 