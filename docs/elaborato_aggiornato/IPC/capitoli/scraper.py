import csv
import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
euristica_path = os.path.join(base_dir, "03_valutazione_euristica.tex")
risultati_path = os.path.join(base_dir, "06_analisi_risultati.tex")

# 1. Scrape Heuristic Evaluation
def scrape_heuristics(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()
    
    # We find all tabularx blocks
    # Each block is for a problem
    # Then we find the comment right after \end{table}
    
    problems = []
    
    table_pattern = re.compile(
        r"\\begin\{tabularx\}.*?\\textbf\{\\problemstep\}\s*&\s*\\textbf\{(.*?)\}\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Posizione\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Descrizione\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Principi violati\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Numero valutatori\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Grado di severità\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\end\{tabularx\}.*?\\end\{table\}\s*%\s*(.*?)\n",
        re.DOTALL | re.MULTILINE
    )
    
    for match in table_pattern.finditer(content):
        title = match.group(1).strip()
        posizione = match.group(2).strip()
        descrizione = match.group(3).strip()
        principi_violati = match.group(4).strip()
        num_valutatori = match.group(5).strip()
        grado_severita = match.group(6).strip()
        valutatori = match.group(7).strip()
        
        # In caso la regex non matchi i % (a volte c'è roba in mezzo o va a capo dopo)
        if hasattr(valutatori, 'strip'):
            valutatori = valutatori.strip()
            
        problems.append({
            "Problema": title,
            "Principi_violati": principi_violati,
            "Numero_valutatori": num_valutatori,
            "Chi": valutatori,
            "Grado_di_severità": grado_severita
        })
        
    # Write to CSV
    csv_path = os.path.join(base_dir, "problemi_euristica.csv")
    with open(csv_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["Problema", "Principi_violati", "Numero_valutatori", "Chi", "Grado_di_severità"])
        writer.writeheader()
        writer.writerows(problems)
    print(f"[{len(problems)}] problemi euristiche estratti in {csv_path}")

# 2. Scrape Task Results
def scrape_task_results(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    tasks = []
    
    # subsection{Compito X: Nome}
    task_pattern = re.compile(
        r"\\subsection\{Compito\s*(\d+):\s*(.*?)\}.*?"
        r"\\begin\{tabular\}.*?"
        r"Completato\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"Tempo Medio\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"Click Medi\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"Errori Medi\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"Difficoltà\s*\(.*?\)\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"Origine\s*\(.*?\)\s*&\s*(.*?)\s*&\s*(.*?)\s*\\\\\s*\\hline",
        re.DOTALL
    )
    
    for match in task_pattern.finditer(content):
        task_num = match.group(1).strip()
        task_name = match.group(2).strip()
        
        comps_v1, comps_v2 = match.group(3).strip(), match.group(4).strip()
        time_v1, time_v2 = match.group(5).strip(), match.group(6).strip()
        clicks_v1, clicks_v2 = match.group(7).strip(), match.group(8).strip()
        errs_v1, errs_v2 = match.group(9).strip(), match.group(10).strip()
        diff_v1, diff_v2 = match.group(11).strip(), match.group(12).strip()
        orig_v1, orig_v2 = match.group(13).strip(), match.group(14).strip()
        
        tasks.append({
             "Task_ID": task_num,
             "Task_Name": task_name,
             "Versione": "V1",
             "Completato": comps_v1,
             "Tempo_Medio": time_v1,
             "Click_Medi": clicks_v1,
             "Errori_Medi": errs_v1,
             "Difficoltà": diff_v1,
             "Origine": orig_v1
        })
        tasks.append({
             "Task_ID": task_num,
             "Task_Name": task_name,
             "Versione": "V2",
             "Completato": comps_v2,
             "Tempo_Medio": time_v2,
             "Click_Medi": clicks_v2,
             "Errori_Medi": errs_v2,
             "Difficoltà": diff_v2,
             "Origine": orig_v2
        })
        
    # Write to CSV
    csv_path = os.path.join(base_dir, "risultati_task.csv")
    with open(csv_path, "w", newline="", encoding="utf-8") as f:
        fieldnames = ["Task_ID", "Task_Name", "Versione", "Completato", "Tempo_Medio", "Click_Medi", "Errori_Medi", "Difficoltà", "Origine"]
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(tasks)
    print(f"[{len(tasks)//2}] task results (V1 e V2) estratti in {csv_path}")


if __name__ == "__main__":
    scrape_heuristics(euristica_path)
    scrape_task_results(risultati_path)
