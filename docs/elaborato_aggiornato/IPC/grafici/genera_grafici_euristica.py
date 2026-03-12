import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import os

# Create relative paths
script_dir = os.path.dirname(os.path.abspath(__file__))
out_dir = os.path.join(script_dir, "../capitoli/immagini/")
os.makedirs(out_dir, exist_ok=True)

# Data
# Severità
severita_counts = {1: 20, 2: 16, 3: 4, 4: 1}
severita_labels = [f"Sev {k}" for k in severita_counts.keys()]
severita_values = list(severita_counts.values())

# Principi
principi_counts = {1: 13, 2: 8, 3: 3, 4: 11, 5: 5, 6: 5, 7: 6, 8: 7, 9: 2, 10: 1}
principi_labels = [f"P{k}" for k in principi_counts.keys()]
principi_values = list(principi_counts.values())

# Problemi e princípi violati (estratti dall'analisi)
problemi_principi = {
    1: [4], 2: [1], 3: [1, 2], 4: [4], 5: [5, 7], 6: [2], 7: [1, 4], 8: [1], 9: [7], 10: [2],
    11: [5], 12: [1, 2, 4], 13: [1, 4], 14: [2], 15: [2], 16: [7], 17: [4, 7], 18: [8], 19: [1, 3, 5], 20: [3, 9],
    21: [1, 6], 22: [4, 7], 23: [8], 24: [5], 25: [1, 6], 26: [1, 6], 27: [3], 28: [4], 29: [1, 6], 30: [4, 8],
    31: [5], 32: [8, 9], 33: [2, 8], 34: [1, 2], 35: [1, 8], 36: [4], 37: [7], 38: [10], 39: [8], 40: [6],
    41: [4]
}

# 1. Grafico Severità (Bar chart)
plt.figure(figsize=(7, 5))
bars = plt.bar(severita_labels, severita_values, color=['#0dcaf0', '#ffc107', '#fd7e14', '#dc3545'])
plt.title("Distribuzione dei Problemi per Grado di Severità", fontsize=14)
plt.xlabel("Grado di Severità", fontsize=12)
plt.ylabel("Numero di Problemi", fontsize=12)
for bar in bars:
    yval = bar.get_height()
    plt.text(bar.get_x() + bar.get_width()/2, yval + 0.3, int(yval), ha='center', va='bottom', fontsize=11)
plt.ylim(0, max(severita_values) + 3)
plt.tight_layout()
plt.savefig(os.path.join(out_dir, "grafico_severita.png"), dpi=300)
plt.close()

# 2. Grafico Principi (Bar chart)
plt.figure(figsize=(9, 5))
bars = plt.bar(principi_labels, principi_values, color='#0d6efd')
plt.title("Frequenza di Violazione dei 10 Principi di Nielsen", fontsize=14)
plt.xlabel("Principi di Nielsen", fontsize=12)
plt.ylabel("Numero di Violazioni", fontsize=12)
for bar in bars:
    yval = bar.get_height()
    plt.text(bar.get_x() + bar.get_width()/2, yval + 0.3, int(yval), ha='center', va='bottom', fontsize=11)
plt.ylim(0, max(principi_values) + 3)
plt.tight_layout()
plt.savefig(os.path.join(out_dir, "grafico_principi.png"), dpi=300)
plt.close()

# 3. Matrice Errori (Grid map)
matrix = np.zeros((10, 41))
for prob_idx, violated in problemi_principi.items():
    for p in violated:
        matrix[p-1, prob_idx-1] = 1

plt.figure(figsize=(15, 5))
# Use pcolor or pcolormesh to draw grid
cmap = plt.cm.get_cmap('Blues', 2)
# Create meshgrid for exact cell alignment
X, Y = np.meshgrid(np.arange(42), np.arange(11))
plt.pcolormesh(X, Y, matrix, edgecolors='lightgrey', linewidth=0.5, cmap=cmap)
plt.gca().invert_yaxis()

# Setup ticks
plt.yticks(np.arange(0.5, 10.5, 1), [f"P{i}" for i in range(1, 11)])
plt.xticks(np.arange(0.5, 41.5, 1), [str(i) for i in range(1, 42)], rotation=0, fontsize=9)
plt.title("Matrice dei Problemi per Principio Violato", fontsize=14)
plt.xlabel("Numero Problema", fontsize=12)
plt.ylabel("Principio Violato", fontsize=12)

# Set equal aspect ratio so squares look nice, if wanted, but 41 columns is very wide.
# A small adjustment
plt.tight_layout()
plt.savefig(os.path.join(out_dir, "matrice_errori.pdf"), format='pdf', bbox_inches="tight")
plt.close()
# 4. Grafico contributo valutatori
tex_path = os.path.join(script_dir, "../capitoli/03_valutazione_euristica.tex")
with open(tex_path, "r", encoding='utf-8') as f:
    text = f.read()

tables = text.split("\\begin{table}")[1:]
results = []
for t in tables:
    if "\\end{table}" in t:
        after_table = t.split("\\end{table}")[1]
        lines = after_table.split("\n")
        evaluator_str = ""
        for line in lines:
            line = line.strip()
            if not line:
                continue
            if line.startswith("%"):
                line_lower = line.lower()
                if "risolto" in line_lower or "capito" in line_lower or "migliorare" in line_lower:
                    continue
                evaluator_str = line
                break
            else:
                break
        
        if not evaluator_str or "tutti" in evaluator_str.lower():
            results.append("tutti")
        else:
            results.append(evaluator_str)

giorgio_count = sum(1 for x in results if 'g' in x.lower() or 'tutti' in x.lower())
daniel_count = sum(1 for x in results if 'd' in x.lower() or 'tutti' in x.lower())
marco_count = sum(1 for x in results if 'm' in x.lower() or 'tutti' in x.lower())

plt.figure(figsize=(7, 5))
evaluators = ['Giorgio (G)', 'Daniel (D)', 'Marco (M)']
counts = [giorgio_count, daniel_count, marco_count]
bars = plt.bar(evaluators, counts, color=['#198754', '#0d6efd', '#ffc107'])
plt.title("Contributo dei Valutatori (Problemi Trovati)", fontsize=14)
plt.xlabel("Valutatore", fontsize=12)
plt.ylabel("Numero di Problemi Individuati", fontsize=12)
for bar in bars:
    yval = bar.get_height()
    plt.text(bar.get_x() + bar.get_width()/2, yval + 0.3, int(yval), ha='center', va='bottom', fontsize=11)
plt.ylim(0, max(counts) + 5)
plt.tight_layout()
plt.savefig(os.path.join(out_dir, "grafico_valutatori.png"), dpi=300)
plt.close()

print("Grafici generati con successo in capitoli/immagini/")
