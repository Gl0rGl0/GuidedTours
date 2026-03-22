import csv
import re
import os

base_dir = "/home/giorgio/Desktop/UNI/IPC/GuidedToursV2/docs/elaborato_aggiornato/IPC/capitoli"
euristica_path = os.path.join(base_dir, "03_valutazione_euristica.tex")

def scrape_heuristics(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    problems = []

    table_pattern = re.compile(
        r"\\begin\{tabularx\}.*?\\textbf\{\\problemstep\}\s*&\s*\\textbf\{(.*?)\}\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Principi violati\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Numero valutatori\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\textbf\{Grado di severità\}\s*&\s*(.*?)\s*\\\\\s*\\hline.*?"
        r"\\end\{tabularx\}.*?\\end\{table\}",
        re.DOTALL
    )

    for match in table_pattern.finditer(content):
        title = match.group(1).strip()
        principi_violati = match.group(2).strip()
        num_valutatori = match.group(3).strip()
        grado_severita = match.group(4).strip()
        
        # Now we look at the characters immediately following \end{table}
        end_pos = match.end()
        # Read the next lines until we find a non-empty line
        chi = "N/D"
        lines = content[end_pos:].split('\n')
        # We only check the very first non-empty lines right after \end{table}
        for line in lines:
            if not line.strip():
                continue
            if line.strip().startswith('%'):
                chi_cand = line.strip().strip('% \t')
                # Ignore states like RISOLTO, DA MIGLIORARE, ecc.
                if chi_cand not in ['RISOLTO', 'DA MIGLIORARE', 'IRRISOLVIBILE', "QUESTO NON L'HO CAPITO", 'NON HO CAPITO', 'RISOLTO CON STILE MENO IMPATTANTE']:
                    chi = chi_cand
                break
            else:
                break # Non-comment text found
                
        problems.append({
            "Problema": title,
            "Principi_violati": principi_violati,
            "Numero_valutatori": num_valutatori,
            "Chi": chi,
            "Grado_di_severità": grado_severita
        })
        
    csv_path = os.path.join(base_dir, "problemi_euristica.csv")
    with open(csv_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["Problema", "Principi_violati", "Numero_valutatori", "Chi", "Grado_di_severità"])
        writer.writeheader()
        writer.writerows(problems)
    print(f"[{len(problems)}] problemi euristiche estratti in {csv_path}")

if __name__ == "__main__":
    scrape_heuristics(euristica_path)
