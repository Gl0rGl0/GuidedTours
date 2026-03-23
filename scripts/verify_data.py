import csv

def round1(v):
    return round(v, 1)

def main():
    # read original
    orig = {}
    with open('capitoli/risultati_task.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for r in reader:
            orig[(r['Task_ID'], r['Versione'])] = r
            
    # read generated
    gen = {}
    with open('dati_utenti_completi.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for r in reader:
            k = (r['Task_ID'], r['Versione'])
            if k not in gen:
                gen[k] = {'Tempo_s': [], 'Click': [], 'Errori': [], 'Difficoltà': [], 'Origine': [], 'Completato': []}
            gen[k]['Tempo_s'].append(int(r['Tempo_s']))
            gen[k]['Click'].append(int(r['Click']))
            gen[k]['Errori'].append(int(r['Errori']))
            gen[k]['Difficoltà'].append(int(r['Difficoltà']))
            gen[k]['Origine'].append(int(r['Origine']))
            gen[k]['Completato'].append(r['Completato'])
            
    # Verify
    diffs = 0
    for k, v in orig.items():
        g = gen.get(k)
        if not g:
            print(f"Missing in generated: {k}")
            diffs += 1
            continue
            
        N = len(g['Tempo_s'])
        if N != 12:
            print(f"Count mismatch: {N} != 12")
            diffs += 1
            
        # Tempo
        avg_t = round1(sum(g['Tempo_s'])/N)
        o_t = float(v['Tempo_Medio'].replace('s',''))
        if avg_t != o_t: 
            print(f"{k} Tempo mismatch: gen {avg_t} vs orig {o_t}")
            diffs += 1
            
        # Click
        avg_c = round1(sum(g['Click'])/N)
        o_c = float(v['Click_Medi'])
        if avg_c != o_c:
            print(f"{k} Click mismatch: gen {avg_c} vs orig {o_c}")
            diffs += 1
            
        # Errori
        avg_e = round1(sum(g['Errori'])/N)
        o_e = float(v['Errori_Medi'])
        if avg_e != o_e:
            print(f"{k} Errori mismatch: gen {avg_e} vs orig {o_e}")
            diffs += 1
            
        # Diff
        avg_d = round1(sum(g['Difficoltà'])/N)
        o_d = float(v['Difficoltà'])
        if avg_d != o_d:
            print(f"{k} Diff mismatch: gen {avg_d} vs orig {o_d}")
            diffs += 1
            
        # Origine
        avg_o = round1(sum(g['Origine'])/N)
        o_o = float(v['Origine'])
        if avg_o != o_o:
            print(f"{k} Orig mismatch: gen {avg_o} vs orig {o_o}")
            diffs += 1
            
    if diffs == 0:
        print("VERIFICATION PASSED EXACTLY.")
    else:
        print(f"FOUND {diffs} DIFFERENCES.")

if __name__ == '__main__':
    main()
