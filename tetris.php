<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego Tetris - Niveles y Puntajes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #282c34;
            color: white;
            text-align: center;
        }
        canvas {
            display: block;
            margin: 20px auto;
            background-color: #000;
        }
        .info {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Juego Tetris</h1>
    <canvas id="tetris" width="240" height="400"></canvas>
    <div class="info">
        <p>Puntaje: <span id="score">0</span></p>
        <p>Nivel: <span id="level">1</span></p>
        <p>Líneas: <span id="lines">0</span></p>
    </div>
    <p>Usa las flechas del teclado para mover y rotar las piezas.</p>

    <script>
        const canvas = document.getElementById('tetris');
        const context = canvas.getContext('2d');
        context.scale(20, 20);

        const arena = createMatrix(12, 20);

        const colors = [
            null,
            '#FF0D72', // T
            '#0DC2FF', // O
            '#0DFF72', // L
            '#F538FF', // J
            '#FF8E0D', // I
            '#FFE138', // S
            '#3877FF', // Z
        ];

        const player = {
            pos: {x: 5, y: 0},
            matrix: createPiece('T'),
            score: 0,
            lines: 0,
            level: 1,
        };

        function createMatrix(width, height) {
            const matrix = [];
            while (height--) {
                matrix.push(new Array(width).fill(0));
            }
            return matrix;
        }

        function createPiece(type) {
            // Igual que antes...
        }

        function drawMatrix(matrix, offset) {
            matrix.forEach((row, y) => {
                row.forEach((value, x) => {
                    if (value !== 0) {
                        context.fillStyle = colors[value];
                        context.fillRect(x + offset.x, y + offset.y, 1, 1);
                    }
                });
            });
        }
        function createPiece(type) {
    switch (type) {
        case 'T':
            return [
                [0, 1, 0],
                [1, 1, 1],
                [0, 0, 0],
            ];
        case 'O':
            return [
                [2, 2],
                [2, 2],
            ];
        case 'L':
            return [
                [0, 0, 3],
                [3, 3, 3],
                [0, 0, 0],
            ];
        case 'J':
            return [
                [4, 0, 0],
                [4, 4, 4],
                [0, 0, 0],
            ];
        case 'I':
            return [
                [0, 5, 0, 0],
                [0, 5, 0, 0],
                [0, 5, 0, 0],
                [0, 5, 0, 0],
            ];
        case 'S':
            return [
                [0, 6, 6],
                [6, 6, 0],
                [0, 0, 0],
            ];
        case 'Z':
            return [
                [7, 7, 0],
                [0, 7, 7],
                [0, 0, 0],
            ];
        default:
            return [[0]];
    }
}


        function draw() {
            context.fillStyle = '#000';
            context.fillRect(0, 0, canvas.width, canvas.height);

            drawMatrix(arena, {x: 0, y: 0});
            drawMatrix(player.matrix, player.pos);
        }

        function merge(arena, player) {
            player.matrix.forEach((row, y) => {
                row.forEach((value, x) => {
                    if (value !== 0) {
                        arena[y + player.pos.y][x + player.pos.x] = value;
                    }
                });
            });
        }

        function arenaSweep() {
            let rowCount = 1;
            outer: for (let y = arena.length - 1; y >= 0; --y) {
                for (let x = 0; x < arena[y].length; ++x) {
                    if (arena[y][x] === 0) {
                        continue outer;
                    }
                }
                const row = arena.splice(y, 1)[0].fill(0);
                arena.unshift(row);
                ++y;

                player.score += rowCount * 10;
                player.lines++;
                rowCount *= 2;

                // Subir nivel cada 10 líneas
                if (player.lines % 10 === 0) {
                    player.level++;
                    dropInterval -= 50; // Aumenta la velocidad
                    if (dropInterval < 200) dropInterval = 200; // Velocidad mínima
                }
            }
        }

        function collide(arena, player) {
    const [matrix, offset] = [player.matrix, player.pos];
    for (let y = 0; y < matrix.length; ++y) {
        for (let x = 0; x < matrix[y].length; ++x) {
            if (matrix[y][x] !== 0 && 
                (arena[y + offset.y] && arena[y + offset.y][x + offset.x]) !== 0) {
                return true;
            }
        }
    }
    return false;
}


        function playerDrop() {
            player.pos.y++;
            if (collide(arena, player)) {
                player.pos.y--;
                merge(arena, player);
                arenaSweep();
                updateScore();
                playerReset();
            }
            dropCounter = 0;
        }

        function playerMove(dir) {
            player.pos.x += dir;
            if (collide(arena, player)) {
                player.pos.x -= dir;
            }
        }

        function playerReset() {
            const pieces = 'ILJOTSZ';
            player.matrix = createPiece(pieces[pieces.length * Math.random() | 0]);
            player.pos.y = 0;
            player.pos.x = (arena[0].length / 2 | 0) - (player.matrix[0].length / 2 | 0);

            if (collide(arena, player)) {
                alert(`Juego terminado. Tu puntaje final fue ${player.score}`);
                arena.forEach(row => row.fill(0));
                player.score = 0;
                player.lines = 0;
                player.level = 1;
                dropInterval = 1000;
                updateScore();
            }
        }
        function rotate(matrix, dir) {
    // Transponer la matriz
    for (let y = 0; y < matrix.length; ++y) {
        for (let x = 0; x < y; ++x) {
            [matrix[x][y], matrix[y][x]] = [matrix[y][x], matrix[x][y]];
        }
    }
    // Invertir filas para rotar en el sentido horario
    if (dir > 0) {
        matrix.forEach(row => row.reverse());
    } else {
        // Invertir columnas para rotar en el sentido antihorario
        matrix.reverse();
    }
}
document.addEventListener('keydown', event => {
    if (event.keyCode === 37) {
        // Mover a la izquierda
        playerMove(-1);
    } else if (event.keyCode === 39) {
        // Mover a la derecha
        playerMove(1);
    } else if (event.keyCode === 40) {
        // Caída rápida
        playerDrop();
    } else if (event.keyCode === 38) {
        // Rotar en el sentido de las agujas del reloj
        playerRotate(1);
    }
});
function playerRotate(dir) {
    const pos = player.pos.x; // Guardar la posición inicial
    let offset = 1; // Desplazamiento inicial
    rotate(player.matrix, dir);

    // Corregir colisiones tras la rotación
    while (collide(arena, player)) {
        player.pos.x += offset; // Ajustar horizontalmente
        offset = -(offset + (offset > 0 ? 1 : -1));
        if (offset > player.matrix[0].length) {
            // Revertir rotación si no hay espacio
            rotate(player.matrix, -dir);
            player.pos.x = pos;
            return;
        }
    }
}

        function updateScore() {
            document.getElementById('score').innerText = player.score;
            document.getElementById('level').innerText = player.level;
            document.getElementById('lines').innerText = player.lines;
        }

        let dropCounter = 0;
        let dropInterval = 1000;

        let lastTime = 0;

        function update(time = 0) {
            const deltaTime = time - lastTime;
            lastTime = time;

            dropCounter += deltaTime;
            if (dropCounter > dropInterval) {
                playerDrop();
            }

            draw();
            requestAnimationFrame(update);
        }

        document.addEventListener('keydown', event => {
            if (event.keyCode === 37) {
                playerMove(-1);
            } else if (event.keyCode === 39) {
                playerMove(1);
            } else if (event.keyCode === 40) {
                playerDrop();
            }
        });

        playerReset();
        update();
        updateScore();
    </script>
</body>
</html>
