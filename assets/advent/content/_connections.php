<?php
$config = $connectionsConfig ?? [];

$gameId = $config['id'] ?? ('connections-' . bin2hex(random_bytes(4)));
$title = $config['title'] ?? 'Connections';
$subtitle = $config['subtitle'] ?? 'Find four groups of four. Pick four words, then hit Submit.';
$footer = $config['footer'] ?? 'Some categories are kinder than others.';
$introMessage = $config['intro_message'] ?? 'Find the hidden groups.';
$groups = $config['groups'] ?? [];

$jsonConfig = json_encode([
	'groups' => $groups,
	'introMessage' => $introMessage,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<div id="<?= htmlspecialchars($gameId, ENT_QUOTES, 'UTF-8') ?>" class="connections-game">
  <style>
    .connections-game {
      --cg-bg: linear-gradient(160deg, #f5efe1 0%, #efe0bb 100%);
      --cg-panel: #fffaf0;
      --cg-text: #2f2a21;
      --cg-muted: #6b6253;
      --cg-border: rgba(69, 53, 28, 0.12);
      --cg-selected: #201f1c;
      --cg-yellow: #f4dd63;
      --cg-green: #9ccc65;
      --cg-blue: #7dc4e4;
      --cg-purple: #b39ddb;
      color: var(--cg-text);
      font-family: "Trebuchet MS", "Avenir Next", sans-serif;
    }

    .connections-shell {
      background: var(--cg-bg);
      border: 1px solid var(--cg-border);
      border-radius: 1.4rem;
      padding: 1.25rem;
      box-shadow: 0 1rem 2.25rem rgba(76, 59, 29, 0.16);
    }

    .connections-header h3 {
      margin: 0;
      font-size: 1.55rem;
      font-weight: 800;
      letter-spacing: 0.02em;
    }

    .connections-header p {
      margin: 0.35rem 0 0;
      color: var(--cg-muted);
    }

    .connections-status {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      margin: 1rem 0 1.1rem;
      padding: 0.8rem 0.95rem;
      background: rgba(255, 255, 255, 0.55);
      border-radius: 1rem;
      border: 1px solid rgba(69, 53, 28, 0.08);
    }

    .connections-status strong {
      font-weight: 800;
    }

    .mistakes-dots {
      display: inline-flex;
      gap: 0.35rem;
      vertical-align: middle;
      margin-left: 0.35rem;
    }

    .mistakes-dots span {
      width: 0.7rem;
      height: 0.7rem;
      border-radius: 999px;
      background: #d34848;
      box-shadow: 0 0 0.45rem rgba(211, 72, 72, 0.35);
    }

    .mistakes-dots span.used {
      background: rgba(211, 72, 72, 0.2);
      box-shadow: none;
    }

    .connections-message {
      min-height: 1.5rem;
      font-weight: 700;
      color: #7a5518;
    }

    .connections-board {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 0.65rem;
    }

    .connection-group {
      grid-column: 1 / -1;
      padding: 0.95rem 1rem;
      border-radius: 1rem;
      color: #1f1b15;
      box-shadow: inset 0 -0.1rem 0 rgba(0, 0, 0, 0.06);
    }

    .connection-group strong,
    .connection-group span {
      display: block;
      text-align: center;
    }

    .connection-group span {
      margin-top: 0.3rem;
      font-size: 0.92rem;
      letter-spacing: 0.04em;
    }

    .cg-tile {
      appearance: none;
      border: 0;
      border-radius: 1rem;
      background: var(--cg-panel);
      color: var(--cg-text);
      min-height: 4.5rem;
      padding: 0.75rem 0.4rem;
      font-size: 0.98rem;
      font-weight: 800;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      box-shadow:
        inset 0 -0.14rem 0 rgba(0, 0, 0, 0.08),
        0 0.45rem 0.8rem rgba(111, 86, 40, 0.08);
      transition: transform 140ms ease, box-shadow 140ms ease, background 140ms ease, color 140ms ease;
    }

    .cg-tile:hover {
      transform: translateY(-2px);
      box-shadow:
        inset 0 -0.14rem 0 rgba(0, 0, 0, 0.08),
        0 0.7rem 1rem rgba(111, 86, 40, 0.12);
    }

    .cg-tile.is-selected {
      background: var(--cg-selected);
      color: #fffdf8;
      transform: translateY(-2px) scale(1.01);
      box-shadow: 0 0.8rem 1.4rem rgba(32, 31, 28, 0.28);
    }

    .cg-tile:disabled {
      cursor: default;
      opacity: 0.62;
      transform: none;
      box-shadow: none;
    }

    .cg-controls {
      display: flex;
      flex-wrap: wrap;
      gap: 0.7rem;
      justify-content: center;
      margin-top: 1.15rem;
    }

    .cg-controls button {
      appearance: none;
      border: 0;
      border-radius: 999px;
      padding: 0.7rem 1rem;
      font-weight: 800;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      color: #fffdf8;
      background: #4a3b22;
      box-shadow: 0 0.5rem 1rem rgba(74, 59, 34, 0.22);
    }

    .cg-controls button.alt {
      background: #857454;
    }

    .cg-controls button:disabled {
      opacity: 0.45;
      box-shadow: none;
    }

    .cg-footer {
      margin-top: 1rem;
      text-align: center;
      color: var(--cg-muted);
      font-size: 0.92rem;
    }

    @media (max-width: 575px) {
      .connections-shell {
        padding: 1rem;
      }

      .connections-board {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .cg-tile {
        min-height: 4rem;
        font-size: 0.9rem;
      }
    }
  </style>

  <div class="connections-shell">
    <div class="connections-header">
      <h3><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
      <p><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="connections-status">
      <div>
        <strong>Mistakes remaining</strong>
        <span class="mistakes-dots" data-mistakes></span>
      </div>
      <div class="connections-message" data-message><?= htmlspecialchars($introMessage, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div class="connections-board" data-board></div>

    <div class="cg-controls">
      <button type="button" class="alt" data-action="shuffle">Shuffle</button>
      <button type="button" class="alt" data-action="clear">Deselect All</button>
      <button type="button" data-action="submit">Submit</button>
    </div>

    <p class="cg-footer"><?= htmlspecialchars($footer, ENT_QUOTES, 'UTF-8') ?></p>
  </div>

  <script>
    (() => {
      const root = document.getElementById(<?= json_encode($gameId) ?>);
      if (!root || root.dataset.initialized === 'true') {
        return;
      }
      root.dataset.initialized = 'true';

      const config = <?= $jsonConfig ?>;
      const groups = config.groups || [];
      const messageEl = root.querySelector('[data-message]');
      const boardEl = root.querySelector('[data-board]');
      const mistakesEl = root.querySelector('[data-mistakes]');
      const controls = root.querySelectorAll('[data-action]');
      const maxMistakes = 4;

      let mistakes = 0;
      let solved = [];
      let selected = [];
      let availableTiles = [];
      let wrongGuesses = new Set();

      function shuffle(array) {
        const copy = [...array];
        for (let i = copy.length - 1; i > 0; i -= 1) {
          const j = Math.floor(Math.random() * (i + 1));
          [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
      }

      function flatTiles() {
        return groups.flatMap((group) =>
          group.words.map((word) => ({
            word,
            groupName: group.name
          }))
        );
      }

      function renderMistakes() {
        mistakesEl.innerHTML = '';
        for (let i = 0; i < maxMistakes; i += 1) {
          const dot = document.createElement('span');
          if (i < mistakes) {
            dot.classList.add('used');
          }
          mistakesEl.appendChild(dot);
        }
      }

      function setMessage(text) {
        messageEl.textContent = text;
      }

      function renderSolvedGroup(group) {
        const panel = document.createElement('div');
        panel.className = 'connection-group';
        panel.style.background = group.color;
        panel.innerHTML = `<strong>${group.name}</strong><span>${group.words.join(' • ')}</span>`;
        boardEl.appendChild(panel);
      }

      function renderBoard() {
        boardEl.innerHTML = '';

        solved.forEach(renderSolvedGroup);

        availableTiles.forEach((tile) => {
          const button = document.createElement('button');
          button.type = 'button';
          button.className = 'cg-tile';
          button.textContent = tile.word;
          button.dataset.word = tile.word;
          button.dataset.group = tile.groupName;
          if (selected.includes(tile.word)) {
            button.classList.add('is-selected');
          }
          button.addEventListener('click', () => toggleTile(tile.word));
          boardEl.appendChild(button);
        });

        const submit = root.querySelector('[data-action="submit"]');
        const clear = root.querySelector('[data-action="clear"]');
        submit.disabled = selected.length !== 4 || solved.length === groups.length || mistakes >= maxMistakes;
        clear.disabled = selected.length === 0;
      }

      function toggleTile(word) {
        if (solved.length === groups.length || mistakes >= maxMistakes) {
          return;
        }

        if (selected.includes(word)) {
          selected = selected.filter((item) => item !== word);
        } else if (selected.length < 4) {
          selected = [...selected, word];
        } else {
          setMessage('You can only select four at a time.');
        }

        renderBoard();
      }

      function remainingGroups() {
        return groups.filter((group) => !solved.some((item) => item.name === group.name));
      }

      function guessKey(words) {
        return [...words].sort().join('|');
      }

      function clearSelection() {
        selected = [];
        setMessage('Selection cleared.');
        renderBoard();
      }

      function shuffleAvailable() {
        availableTiles = shuffle(availableTiles);
        setMessage('Board shuffled.');
        renderBoard();
      }

      function completeGame(message) {
        selected = [];
        availableTiles = [];
        setMessage(message);
        renderBoard();
      }

      function revealAfterLoss() {
        solved = [...groups];
        completeGame('Out of mistakes. Here were the completed groups.');
      }

      function submitGuess() {
        if (selected.length !== 4) {
          setMessage('Select exactly four words first.');
          return;
        }

        const currentGuessKey = guessKey(selected);

        const matches = remainingGroups().find((group) =>
          group.words.every((word) => selected.includes(word))
        );

        if (matches) {
          solved = [...solved, matches];
          availableTiles = availableTiles.filter((tile) => !matches.words.includes(tile.word));
          selected = [];

          if (solved.length === groups.length) {
            completeGame('Perfect. You untangled all four groups.');
            return;
          }

          setMessage(`Solved: ${matches.name}`);
          renderBoard();
          return;
        }

        if (wrongGuesses.has(currentGuessKey)) {
          setMessage('You already tried that group.');
          renderBoard();
          return;
        }

        wrongGuesses.add(currentGuessKey);

        mistakes += 1;
        renderMistakes();

        const isOneAway = remainingGroups().some((group) => {
          const overlap = group.words.filter((word) => selected.includes(word)).length;
          return overlap === 3;
        });

        setMessage(isOneAway ? 'One away...' : 'Not a group. Try another combination.');

        if (mistakes >= maxMistakes) {
          revealAfterLoss();
          return;
        }

        renderBoard();
      }

      controls.forEach((control) => {
        control.addEventListener('click', () => {
          switch (control.dataset.action) {
            case 'shuffle':
              shuffleAvailable();
              break;
            case 'clear':
              clearSelection();
              break;
            case 'submit':
              submitGuess();
              break;
            default:
              break;
          }
        });
      });

      availableTiles = shuffle(flatTiles());
      renderMistakes();
      renderBoard();
    })();
  </script>
</div>
