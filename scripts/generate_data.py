import csv
import random
import math
import re

def find_int_sum(avg, N=12, decimals=1):
    for S in range(max(0, int(avg*N - N)), int(avg*N + N + 1)):
        if round(tuple_avg(S, N), decimals) == avg:
            return S
    return int(round(avg*N))

def tuple_avg(S, N):
    return S / N

def distribute_int(target_sum, count, min_val):
    if target_sum < count * min_val:
        v = [min_val]*count
        # just to not crash, though means average is impossible
        return v
    vals = [min_val] * count
    rem = target_sum - count * min_val
    for _ in range(rem):
        vals[random.randint(0, count-1)] += 1
    return vals

def distribute_for_status(target_sum, count, min_val, max_val, statuses):
    # distribute elements such that 'C' gets lower, 'NC' gets higher
    # We will generate N ints, sort them, and assign based on status
    if target_sum < count * min_val: 
        target_sum = count * min_val
    
    vals = [min_val] * count
    remaining = target_sum - sum(vals)
    while remaining > 0:
        idx = random.randint(0, count-1)
        if max_val is None or vals[idx] < max_val:
            vals[idx] += 1
            remaining -= 1
            
    vals.sort()
    
    # statuses is list of 'C','CA','NC'. 
    # Zip and sort statuses to know how many to assign
    # Actually wait. Let's just group by status:
    # C should get the smallest, CA middle, NC largest.
    status_order = {'C': 0, 'CA': 1, 'NC': 2}
    # Create indices based on status
    sorted_status_indices = sorted(range(count), key=lambda x: status_order[statuses[x]])
    
    res = [0] * count
    for i, idx in enumerate(sorted_status_indices):
        res[idx] = vals[i]
    return res

def parse_completato(s):
    res = []
    for part in s.split(','):
        part = part.strip()
        m = re.match(r'(\d+)\s*\((.*?)\)', part)
        if m:
            c = int(m.group(1))
            st = m.group(2)
            res.extend([st]*c)
    while len(res) < 12:
        res.append('C')
    return res[:12]

def read_users():
    users = []
    with open('profili_utente.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f, delimiter=';')
        for i, row in enumerate(reader):
            # create Group A for first 6, Group B for last 6
            row['Gruppo'] = 'A' if i < 6 else 'B'
            # fix empty competenze just in case
            comp = row.get('Competenze Informatiche', '3').strip()
            if not comp: comp = '3'
            row['Competenze_Score'] = int(comp)
            users.append(row)
    return users

def read_tasks():
    tasks = []
    with open('capitoli/risultati_task.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f, delimiter=',')
        for row in reader:
            tasks.append(row)
    return tasks

def main():
    users = read_users()
    tasks = read_tasks()
    
    # We want to match good users with good tuples.
    # User's goodness = Competenze_Score
    users_sorted_indices = sorted(range(12), key=lambda x: users[x]['Competenze_Score'], reverse=True)
    
    out_rows = []
    
    for t in tasks:
        task_id = t['Task_ID']
        task_name = t['Task_Name']
        versione = t['Versione']
        
        comps = parse_completato(t['Completato'])
        
        tempo_avg_str = t['Tempo_Medio'].replace('s', '').strip()
        tempo_avg = float(tempo_avg_str)
        tempo_sum = int(round(tempo_avg * 12))
        
        click_avg = float(t['Click_Medi'])
        click_sum = find_int_sum(click_avg, 12, decimals=1)
        # click min could be 1
        
        err_avg = float(t['Errori_Medi'])
        err_sum = find_int_sum(err_avg, 12, decimals=1)
        
        diff_avg = float(t['Difficoltà'])
        diff_sum = find_int_sum(diff_avg, 12, decimals=1)
        
        orig_avg = float(t['Origine'])
        orig_sum = find_int_sum(orig_avg, 12, decimals=1)
        
        # Sort comps so that best statuses come first 
        # Actually generate for comps:
        # To just be simple, we can generate sorted lists of metrics, 
        # and zip them to form "best" to "worst" tuples.
        
        T_vals = distribute_for_status(tempo_sum, 12, 1, None, comps)
        # add a bit of noise so averages are not artificially perfect
        T_vals = [max(1, round(t + random.uniform(-1, 1))) for t in T_vals]
        # re-sort T_vals to just be ascending so we can make coherent tuples
        T_vals.sort()
        
        C_vals = distribute_int(click_sum, 12, 1)
        # small noise for clicks too
        C_vals = [max(1, round(c + random.uniform(-1, 1))) for c in C_vals]
        C_vals.sort()
        
        E_vals = distribute_int(err_sum, 12, 0)
        E_vals.sort()
        
        D_vals = distribute_int(diff_sum, 12, 1)
        D_vals.sort()
        
        O_vals = distribute_int(orig_sum, 12, 1)
        O_vals.sort()
        
        comps_sorted = sorted(comps, key=lambda x: {'C':0, 'CA':1, 'NC':2}[x])
        
        # Now we have 12 tuples ordered from best to worst performance.
        # Zip them together
        tuples_sorted = list(zip(comps_sorted, T_vals, C_vals, E_vals, D_vals, O_vals))
        
        # Now map to users: users_sorted_indices has indices of users from best to worst computer skills.
        # We assign the i-th best tuple to the i-th best user!
        user_assignments = [None] * 12
        for i_rank, user_idx in enumerate(users_sorted_indices):
            user_assignments[user_idx] = tuples_sorted[i_rank]
            
        for user_idx in range(12):
            u = users[user_idx]
            comp, T, C, E, D, O = user_assignments[user_idx]
            
            # format values nicely
            row = {
                'Utente_ID': str(user_idx + 1),
                'Nome': u['Nome'],
                'Cognome': u['Cognome'],
                'Gruppo': u['Gruppo'],
                'Task_ID': task_id,
                'Task_Name': task_name,
                'Versione': versione,
                'Completato': comp,
                'Tempo_s': T,
                'Click': C,
                'Errori': E,
                'Difficoltà': D,
                'Origine': O
            }
            out_rows.append(row)

    # write output
    fields = ['Utente_ID', 'Nome', 'Cognome', 'Gruppo', 'Task_ID', 'Task_Name', 'Versione', 
              'Completato', 'Tempo_s', 'Click', 'Errori', 'Difficoltà', 'Origine']
    with open('dati_utenti_completi.csv', 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=fields, delimiter=',')
        writer.writeheader()
        for r in out_rows:
            writer.writerow(r)
            
    print("DONE. Generated", len(out_rows), "rows.")

if __name__ == '__main__':
    main()
