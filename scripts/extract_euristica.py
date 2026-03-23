import re
import csv
import os

def parse_latex_detailed(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Improved regex: find table blocks, then extract inner fields
    table_pattern = re.compile(r'\\begin\{table\}.*?\\end\{table\}', re.DOTALL)
    
    problems = {}
    for match in table_pattern.finditer(content):
        table_content = match.group(0)
        
        # Extract ID and Title from Caption
        cap_m = re.search(r'\\caption\{(P\d+)\s*--\s*(.*?)\}', table_content)
        if not cap_m:
            continue
            
        p_id = cap_m.group(1)
        title = cap_m.group(2).strip()
        
        # Look for a comment immediately AFTER \end{table}
        # We search in the original content starting from the end of this match
        end_pos = match.end()
        # Look for a comment on the next few lines (max 50 chars)
        comment_search_area = content[end_pos : end_pos + 100]
        comment_m = re.search(r'^\s*%\s*(.*)', comment_search_area)
        comment = comment_m.group(1).strip() if comment_m else ''
        if comment:
            # Drop everything after first newline if any
            comment = comment.split('\n')[0].strip()

        # Extract fields inside the tabularx
        pos_m = re.search(r'\\textbf\{Posizione\} & (.*?) \\\\', table_content)
        desc_m = re.search(r'\\textbf\{Descrizione\} & (.*?) \\\\', table_content)
        princ_m = re.search(r'\\textbf\{Principi violati\} & (.*?) \\\\', table_content)
        eval_m = re.search(r'\\textbf\{Numero valutatori\} & (.*?) \\\\', table_content)
        sev_m = re.search(r'\\textbf\{Grado di severità\} & (.*?) \\\\', table_content)
        
        problems[p_id] = {
            'ID': p_id,
            'Titolo': title,
            'Posizione': pos_m.group(1).strip() if pos_m else '',
            'Descrizione': desc_m.group(1).strip() if desc_m else '',
            'Principi_Violati': princ_m.group(1).strip() if princ_m else '',
            'Num_Valutatori': eval_m.group(1).strip() if eval_m else '',
            'Severità': sev_m.group(1).strip() if sev_m else '',
            'Chi': comment if any(c in comment for c in 'DGM') else ''
        }
    return problems

def merge_with_csv(problems, csv_path):
    if not os.path.exists(csv_path):
        return problems
    
    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for i, row in enumerate(reader, 1):
            p_id = f"P{i}"
            if p_id in problems:
                chi = row.get('Chi', '').strip()
                if chi:
                    if chi.lower() == 'tutti':
                        chi = 'D, G, M'
                    if chi != 'N/D' and chi:
                        # Only override if we don't have it or if CSV has better info
                        problems[p_id]['Chi'] = chi
    return problems

def main():
    tex_path = r'c:\Users\Giorg\Desktop\Scuola\Uni\IPC\GuidedTours\docs\elaborato_aggiornato\IPC\capitoli\03_valutazione_euristica.tex'
    short_csv_path = r'c:\Users\Giorg\Desktop\Scuola\Uni\IPC\GuidedTours\docs\elaborato_aggiornato\IPC\capitoli\problemi_euristica.csv'
    out_path = r'c:\Users\Giorg\Desktop\Scuola\Uni\IPC\GuidedTours\docs\elaborato_aggiornato\IPC\valutazione_euristica_completa.csv'
    
    problems = parse_latex_detailed(tex_path)
    problems = merge_with_csv(problems, short_csv_path)
    
    sorted_ids = sorted(problems.keys(), key=lambda x: int(x[1:]))
    
    fields = ['ID', 'Titolo', 'Posizione', 'Descrizione', 'Principi_Violati', 'Num_Valutatori', 'Severità', 'Valutatori']
    with open(out_path, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=fields)
        writer.writeheader()
        for p_id in sorted_ids:
            p = problems[p_id]
            writer.writerow({
                'ID': p['ID'],
                'Titolo': p['Titolo'],
                'Posizione': p['Posizione'],
                'Descrizione': p['Descrizione'],
                'Principi_Violati': p['Principi_Violati'],
                'Num_Valutatori': p['Num_Valutatori'],
                'Severità': p['Severità'],
                'Valutatori': p['Chi']
            })
        
    print(f"DONE. Merged Chi info for {len(problems)} problems into {out_path}")

if __name__ == '__main__':
    main()
