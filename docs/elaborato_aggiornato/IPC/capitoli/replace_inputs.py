import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
f3 = os.path.join(base_dir, "03_valutazione_euristica.tex")
f6 = os.path.join(base_dir, "06_analisi_risultati.tex")

with open(f3, "r", encoding="utf-8") as file:
    content = file.read()
    
# Replace the 3 charts in cap 3
content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Distribuzione dei problemi per grado di severità.*?\}',
                 r'\\input{capitoli/grafici/grafico_severita.tex}\n    \\caption{Distribuzione dei problemi per grado di severità (Scala Nielsen 0-4).}', content)
                 
content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Frequenza di violazione dei 10 Principi.*?\}',
                 r'\\input{capitoli/grafici/grafico_principi.tex}\n    \\caption{Frequenza di violazione dei 10 Principi di Nielsen.}', content)
                 
content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Contributo dei valutatori.*?\}',
                 r'\\input{capitoli/grafici/grafico_valutatori.tex}\n    \\caption{Contributo dei valutatori per numero di problemi individuati.}', content)

with open(f3, "w", encoding="utf-8") as file:
    file.write(content)

with open(f6, "r", encoding="utf-8") as file:
    content = file.read()

# Replace the 3 charts in cap 6
content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Tasso di completamento.*?\}',
                 r'\\input{capitoli/grafici/grafico_completamento.tex}\n    \\caption{Tasso di completamento autonomo (\\%) per compito — V1 vs V2.}', content)

content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Tempo medio di completamento.*?\}',
                 r'\\input{capitoli/grafici/grafico_tempi.tex}\n    \\caption{Tempo medio di completamento (secondi) per compito — V1 vs V2.}', content)

content = re.sub(r'\\begin\{tikzpicture\}[\s\S]*?\\end\{tikzpicture\}\s*\\caption\{Errori medi per.*?\}',
                 r'\\input{capitoli/grafici/grafico_errori.tex}\n    \\caption{Errori medi per compito — V1 vs V2.}', content)

with open(f6, "w", encoding="utf-8") as file:
    file.write(content)

print("Sostituzioni completate nei file tex.")
