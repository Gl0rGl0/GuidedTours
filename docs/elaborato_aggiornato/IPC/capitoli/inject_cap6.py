import csv
import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
tasks_csv = os.path.join(base_dir, "risultati_task.csv")
euristica_csv = os.path.join(base_dir, "problemi_euristica.csv")

# 1. GENERARE GRAFICI CAPITOLO 6 (Analisi Task)
def generate_cap6_charts():
    v1_tempi = []
    v2_tempi = []
    v1_errori = []
    v2_errori = []
    
    # Per il grafico del completamento, stackiamo C, CA, NC
    v1_C = []
    v1_CA = []
    v1_NC = []
    v2_C = []
    v2_CA = []
    v2_NC = []

    with open(tasks_csv, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            t_id = f"T{row['Task_ID']}"
            ver = row["Versione"]
            
            # Extract number from "55 s"
            tempo = row["Tempo_Medio"].replace('s','').strip()
            errori = row["Errori_Medi"]
            
            # Completato string: "8 (C), 3 (CA), 1 (NC)"
            comp = row["Completato"]
            m = re.match(r"(\d+)\s*\(C\),\s*(\d+)\s*\(CA\),\s*(\d+)\s*\(NC\)", comp)
            if m:
                c, ca, nc = m.groups()
            else:
                c, ca, nc = "0", "0", "0"

            if ver == "V1":
                v1_tempi.append(f"({t_id},{tempo})")
                v1_errori.append(f"({t_id},{errori})")
                v1_C.append(f"({t_id},{c})")
                v1_CA.append(f"({t_id},{ca})")
                v1_NC.append(f"({t_id},{nc})")
            else:
                v2_tempi.append(f"({t_id},{tempo})")
                v2_errori.append(f"({t_id},{errori})")
                v2_C.append(f"({t_id},{c})")
                v2_CA.append(f"({t_id},{ca})")
                v2_NC.append(f"({t_id},{nc})")

    # Generate Tempi
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

    # Generate Errori
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

    # Generate Completamento Stacked (V1 vs V2 maybe side by side per task stacked)
    # PGFPlots doesn't easily do side-by-side stacked bars directly. We can do two separate charts or side by side.
    # We will do side by side for comparison, using ybar and just total completion or success rate?
    # Actually, the user's previous code plot was a grouped bar chart by percentage or something. 
    # Let's do just C (Completed) as a simple side-by-side bar chart:
    
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

    return tempi_tex, errori_tex, completamento_tex

def replace_in_file(filepath, completion_tex, tempi_tex, errori_tex):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Sostituire grafico_completamento.png
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_completamento.*?\}', 
                     lambda m: completion_tex, content)

    # Sostituire grafico_tempi.png
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_tempi.*?\}', 
                     lambda m: tempi_tex, content)

    # Sostituire grafico_errori.png
    content = re.sub(r'\\includegraphics\[.*?\]\{capitoli/immagini/grafico_errori.*?\}', 
                     lambda m: errori_tex, content)
                     
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    t_tex, e_tex, c_tex = generate_cap6_charts()
    path6 = os.path.join(base_dir, "06_analisi_risultati.tex")
    replace_in_file(path6, c_tex, t_tex, e_tex)
    print("Sostituiti i grafici in 06_analisi_risultati.tex")
