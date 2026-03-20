import re
import matplotlib.pyplot as plt
import numpy as np
import os

# Configuration
LATEX_FILE = 'docs/elaborato_aggiornato/IPC/capitoli/06_analisi_risultati.tex'
OUTPUT_DIR = 'grafici'

# Colors
COLOR_V1 = '#3498db'  # Blue
COLOR_V2 = '#2ecc71'  # Green
COLORS_OUTCOME = ['#27ae60', '#f1c40f', '#e74c3c']  # C (Green), CA (Yellow), NC (Red)

def parse_latex(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    tasks = []
    # Find all subsections (tasks)
    # \subsection{Compito 1: Sede organizzazione}
    sections = re.findall(r'\\subsection\{Compito (\d+): (.*?)\}(.*?)(?=\\subsection|\\section|\\begin\{figure\}|$)', content, re.DOTALL)

    for id, title, body in sections:
        task_data = {
            'id': int(id),
            'title': title.strip(),
            'v1': {},
            'v2': {}
        }

        # Extract Completato: 8 (C), 3 (CA), 1 (NC) & 11 (C), 1 (CA), 0 (NC)
        comp_match = re.search(r'Completato\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\', body)
        if comp_match:
            v1_raw, v2_raw = comp_match.groups()
            
            def parse_comp(raw):
                c = int(re.search(r'(\d+)\s*\(C\)', raw).group(1)) if '(C)' in raw else 0
                ca = int(re.search(r'(\d+)\s*\(CA\)', raw).group(1)) if '(CA)' in raw else 0
                nc = int(re.search(r'(\d+)\s*\(NC\)', raw).group(1)) if '(NC)' in raw else 0
                return {'C': c, 'CA': ca, 'NC': nc}

            task_data['v1']['comp'] = parse_comp(v1_raw)
            task_data['v2']['comp'] = parse_comp(v2_raw)

        # Extract Tempo Medio: 55 s & 22 s 
        time_match = re.search(r'Tempo Medio\s*&\s*(\d+)\s*s\s*&\s*(\d+)\s*s\s*\\\\', body)
        if time_match:
            task_data['v1']['time'] = int(time_match.group(1))
            task_data['v2']['time'] = int(time_match.group(2))

        # Extract Errori Medi: 2.2 & 0.1
        err_match = re.search(r'Errori Medi\s*&\s*([\d\.]+)\s*&\s*([\d\.]+)\s*\\\\', body)
        if err_match:
            task_data['v1']['errors'] = float(err_match.group(1))
            task_data['v2']['errors'] = float(err_match.group(2))

        tasks.append(task_data)
    
    return tasks

def plot_outcomes(tasks):
    # Sum up totals
    v1_totals = {'C': 0, 'CA': 0, 'NC': 0}
    v2_totals = {'C': 0, 'CA': 0, 'NC': 0}
    
    for t in tasks:
        for k in v1_totals:
            v1_totals[k] += t['v1']['comp'][k]
            v2_totals[k] += t['v2']['comp'][k]

    fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(12, 6))
    labels = ['Completato (C)', 'Con Aiuto (CA)', 'Non Completato (NC)']
    
    def func(pct, allvals):
        absolute = int(np.round(pct/100.*np.sum(allvals)))
        return f"{pct:.1f}%\n({absolute})"

    ax1.pie(v1_totals.values(), autopct=lambda pct: func(pct, list(v1_totals.values())),
            colors=COLORS_OUTCOME, startangle=140, pctdistance=0.85)
    ax1.set_title('Versione 1 (Originale)', pad=20, fontsize=14, fontweight='bold')
    
    ax2.pie(v2_totals.values(), autopct=lambda pct: func(pct, list(v2_totals.values())),
            colors=COLORS_OUTCOME, startangle=140, pctdistance=0.85)
    ax2.set_title('Versione 2 (Riprogettata)', pad=20, fontsize=14, fontweight='bold')

    fig.legend(labels, loc='lower center', ncol=3, fontsize=11, frameon=False)
    plt.tight_layout(rect=[0, 0.1, 1, 1])
    plt.savefig(os.path.join(OUTPUT_DIR, 'esiti_distribuzione.png'), dpi=300)
    plt.close()

def plot_bar_metric(tasks, metric_name, filename, ylabel, title):
    labels = [f"T{t['id']}" for t in tasks]
    v1_values = [t['v1'][metric_name] for t in tasks]
    v2_values = [t['v2'][metric_name] for t in tasks]

    x = np.arange(len(labels))
    width = 0.35

    fig, ax = plt.subplots(figsize=(10, 6))
    rects1 = ax.bar(x - width/2, v1_values, width, label='V1 (Originale)', color=COLOR_V1)
    rects2 = ax.bar(x + width/2, v2_values, width, label='V2 (Riprogettata)', color=COLOR_V2)

    ax.set_ylabel(ylabel, fontsize=12)
    ax.set_title(title, fontsize=14, fontweight='bold', pad=15)
    ax.set_xticks(x)
    ax.set_xticklabels(labels)
    ax.legend()

    ax.bar_label(rects1, padding=3, fontsize=9)
    ax.bar_label(rects2, padding=3, fontsize=9)

    fig.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, filename), dpi=300)
    plt.close()

def plot_completion_bars(tasks):
    labels = [f"T{t['id']}" for t in tasks]
    # completion rate as % of autonomous completion (C)
    # Total users = C + CA + NC = 12
    v1_values = [ (t['v1']['comp']['C'] / 12) * 100 for t in tasks]
    v2_values = [ (t['v2']['comp']['C'] / 12) * 100 for t in tasks]

    x = np.arange(len(labels))
    width = 0.35

    fig, ax = plt.subplots(figsize=(10, 6))
    rects1 = ax.bar(x - width/2, v1_values, width, label='V1 (Originale)', color=COLOR_V1)
    rects2 = ax.bar(x + width/2, v2_values, width, label='V2 (Riprogettata)', color=COLOR_V2)

    ax.set_ylabel('Tasso completamento autonomo (%)', fontsize=12)
    ax.set_title('Completamento Autonomo per Compito', fontsize=14, fontweight='bold', pad=15)
    ax.set_xticks(x)
    ax.set_xticklabels(labels)
    ax.set_ylim(0, 115) # Leave space for labels
    ax.legend()

    ax.bar_label(rects1, fmt='%.0f%%', padding=3, fontsize=9)
    ax.bar_label(rects2, fmt='%.0f%%', padding=3, fontsize=9)

    fig.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'grafico_completamento.png'), dpi=300)
    plt.close()

if __name__ == '__main__':
    print("Parsing LaTeX file...")
    tasks_data = parse_latex(LATEX_FILE)
    
    print(f"Extracted data for {len(tasks_data)} tasks.")
    
    print("Generating Outcome Distribution Pie Charts...")
    plot_outcomes(tasks_data)
    
    print("Generating Time Chart...")
    plot_bar_metric(tasks_data, 'time', 'grafico_tempi.png', 'Tempo (secondi)', 'Tempo Medio di Completamento')
    
    print("Generating Error Chart...")
    plot_bar_metric(tasks_data, 'errors', 'grafico_errori.png', 'Numero medio errori', 'Errori Medi per Compito')
    
    print("Generating Completion Chart...")
    plot_completion_bars(tasks_data)
    
    print("Done! Charts saved in 'grafici' directory.")
