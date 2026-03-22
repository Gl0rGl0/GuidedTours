import csv
import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
grafici_dir = os.path.join(base_dir, "grafici")
tasks_csv = os.path.join(base_dir, "risultati_task.csv")
euristica_csv = os.path.join(base_dir, "problemi_euristica.csv")

# Ensure grafici directory exists
os.makedirs(grafici_dir, exist_ok=True)

def genera_grafici_cap6():
    v1_tempi, v2_tempi = [], []
    v1_errori, v2_errori = [], []
    v1_C, v2_C = [], []

    if not os.path.exists(tasks_csv):
        return

    with open(tasks_csv, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            t_id = f"T{row['Task_ID']}"
            ver = row["Versione"]
            tempo = row["Tempo_Medio"].replace('s','').strip()
            errori = row["Errori_Medi"]
            
            comp = row["Completato"]
            m = re.match(r"(\d+)\s*\(C\),\s*(\d+)\s*\(CA\),\s*(\d+)\s*\(NC\)", comp)
            c = m.group(1) if m else "0"

            if ver == "V1":
                v1_tempi.append(f"({t_id},{tempo})")
                v1_errori.append(f"({t_id},{errori})")
                v1_C.append(f"({t_id},{c})")
            else:
                v2_tempi.append(f"({t_id},{tempo})")
                v2_errori.append(f"({t_id},{errori})")
                v2_C.append(f"({t_id},{c})")

    tempi_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar=0pt,
        bar width=8pt,
        enlarge x limits=0.08,
        legend style={at={(0.5,-0.15)}, anchor=north, legend columns=-1},
        ylabel={Tempi (secondi)},
        symbolic x coords={T1, T2, T3, T4, T5, T6, T7, T8, T9, T10, T11},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        every node near coord/.append style={font=\tiny, rotate=90, anchor=west},
        width=\textwidth,
        height=7cm,
        ymin=0,
        ymax=140
    ]
        \addplot[fill=blue!40] coordinates { """ + " ".join(v1_tempi) + r""" };
        \addplot[fill=green!40!black] coordinates { """ + " ".join(v2_tempi) + r""" };
        \legend{Versione 1, Versione 2}
    \end{axis}
\end{tikzpicture}"""

    errori_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar=0pt,
        bar width=8pt,
        enlarge x limits=0.08,
        legend style={at={(0.5,-0.15)}, anchor=north, legend columns=-1},
        ylabel={Numero Medio Errori},
        symbolic x coords={T1, T2, T3, T4, T5, T6, T7, T8, T9, T10, T11},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        every node near coord/.append style={font=\tiny, rotate=90, anchor=west},
        width=\textwidth,
        height=7cm,
        ymin=0,
        ymax=5
    ]
        \addplot[fill=red!40] coordinates { """ + " ".join(v1_errori) + r""" };
        \addplot[fill=green!40] coordinates { """ + " ".join(v2_errori) + r""" };
        \legend{Versione 1, Versione 2}
    \end{axis}
\end{tikzpicture}"""

    completamento_tex = r"""\begin{tikzpicture}
    \begin{axis}[
        ybar=0pt,
        bar width=8pt,
        enlarge x limits=0.08,
        legend style={at={(0.5,-0.15)}, anchor=north, legend columns=-1},
        ylabel={Completamenti (C) su 12},
        symbolic x coords={T1, T2, T3, T4, T5, T6, T7, T8, T9, T10, T11},
        xtick=data,
        nodes near coords,
        nodes near coords align={vertical},
        every node near coord/.append style={font=\tiny},
        width=\textwidth,
        height=7cm,
        ymin=0,
        ymax=14
    ]
        \addplot[fill=cyan!60] coordinates { """ + " ".join(v1_C) + r""" };
        \addplot[fill=teal!80] coordinates { """ + " ".join(v2_C) + r""" };
        \legend{Versione 1, Versione 2}
    \end{axis}
\end{tikzpicture}"""

    with open(os.path.join(grafici_dir, "grafico_tempi.tex"), "w") as f: f.write(tempi_tex)
    with open(os.path.join(grafici_dir, "grafico_errori.tex"), "w") as f: f.write(errori_tex)
    with open(os.path.join(grafici_dir, "grafico_completamento.tex"), "w") as f: f.write(completamento_tex)


def genera_grafici_cap3():
    severita_counts = {0:0, 1:0, 2:0, 3:0, 4:0}
    principi_counts = {str(i):0 for i in range(1, 11)}
    valutatori_counts = {"D": 0, "G": 0, "M": 0}

    if not os.path.exists(euristica_csv):
        return

    with open(euristica_csv, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            sev = row["Grado_di_severità"].strip()
            if sev.isdigit() and int(sev) in severita_counts:
                severita_counts[int(sev)] += 1
                
            nums = re.findall(r'\b([1-9]|10)\b', row["Principi_violati"])
            for n in nums:
                if n in principi_counts:
                    principi_counts[n] += 1
                    
            chi = row["Chi"]
            if chi:
                if "tutti" in chi.lower():
                    valutatori_counts["D"] += 1; valutatori_counts["G"] += 1; valutatori_counts["M"] += 1
                else:
                    if "D" in chi: valutatori_counts["D"] += 1
                    if "G" in chi: valutatori_counts["G"] += 1
                    if "M" in chi: valutatori_counts["M"] += 1

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

    with open(os.path.join(grafici_dir, "grafico_severita.tex"), "w") as f: f.write(severita_tex)
    with open(os.path.join(grafici_dir, "grafico_principi.tex"), "w") as f: f.write(principi_tex)
    with open(os.path.join(grafici_dir, "grafico_valutatori.tex"), "w") as f: f.write(valutatori_tex)


if __name__ == "__main__":
    genera_grafici_cap6()
    genera_grafici_cap3()
    print("Files .tex dei grafici aggiornati con successo.")
