import csv
import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
euristica_csv = os.path.join(base_dir, "problemi_euristica.csv")

def generate_cap3_charts():
    severita_counts = {0:0, 1:0, 2:0, 3:0, 4:0}
    principi_counts = {str(i):0 for i in range(1, 11)}
    valutatori_counts = {"D": 0, "G": 0, "M": 0}

    with open(euristica_csv, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            # Severità
            sev = row["Grado_di_severità"].strip()
            if sev.isdigit() and int(sev) in severita_counts:
                severita_counts[int(sev)] += 1
                
            # Principi
            principi_raw = row["Principi_violati"]
            nums = re.findall(r'\b([1-9]|10)\b', principi_raw)
            for n in nums:
                if n in principi_counts:
                    principi_counts[n] += 1
                    
            # Valutatori
            chi = row["Chi"]
            if chi:
                if "tutti" in chi.lower():
                    valutatori_counts["D"] += 1
                    valutatori_counts["G"] += 1
                    valutatori_counts["M"] += 1
                else:
                    if "D" in chi: valutatori_counts["D"] += 1
                    if "G" in chi: valutatori_counts["G"] += 1
                    if "M" in chi: valutatori_counts["M"] += 1

    # Grafico Severità
    sev_coords = " ".join([f"({k},{v})" for k,v in severita_counts.items()])
    severita_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar,
        bar width=20pt,
        enlarge x limits=0.15,
        ylabel={Numero di Problemi},
        xlabel={Grado di Severità},
        symbolic x coords={0, 1, 2, 3, 4},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        width=0.8\textwidth,
        height=6cm,
        ymin=0
    ]
        \addplot[fill=orange!60] coordinates { """ + sev_coords + r""" };
    \end{axis}
\end{tikzpicture}"""

    # Grafico Principi
    principi_coords = " ".join([f"(P{k},{v})" for k,v in principi_counts.items()])
    principi_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar,
        bar width=15pt,
        enlarge x limits=0.1,
        ylabel={Numero di Violazioni},
        xlabel={Principio Euristico},
        symbolic x coords={P1, P2, P3, P4, P5, P6, P7, P8, P9, P10},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        width=\textwidth,
        height=6.5cm,
        ymin=0
    ]
        \addplot[fill=purple!50] coordinates { """ + principi_coords + r""" };
    \end{axis}
\end{tikzpicture}"""

    # Grafico Valutatori
    valutatori_coords = " ".join([f"({k},{v})" for k,v in valutatori_counts.items()])
    valutatori_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar,
        bar width=25pt,
        enlarge x limits=0.3,
        ylabel={Problemi Individuati},
        xlabel={Valutatore},
        symbolic x coords={D, G, M},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        width=0.6\textwidth,
        height=6cm,
        ymin=0
    ]
        \addplot[fill=teal!50] coordinates { """ + valutatori_coords + r""" };
    \end{axis}
\end{tikzpicture}"""

    return severita_tex, principi_tex, valutatori_tex

def replace_in_file(filepath, sev_tex, prin_tex, val_tex):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Sostituzione grafico_severita
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_severita.*?\}', 
                     lambda m: sev_tex, content)

    # Sostituzione grafico_principi
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_principi.*?\}', 
                     lambda m: prin_tex, content)

    # Sostituzione grafico_valutatori
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_valutatori.*?\}', 
                     lambda m: val_tex, content)
                     
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    t1, t2, t3 = generate_cap3_charts()
    path3 = os.path.join(base_dir, "03_valutazione_euristica.tex")
    replace_in_file(path3, t1, t2, t3)
    print("Sostituiti i grafici in 03_valutazione_euristica.tex")
