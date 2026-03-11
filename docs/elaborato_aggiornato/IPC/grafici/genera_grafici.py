import pandas as pd
import matplotlib.pyplot as plt

df = pd.read_csv('../profili_utente.csv', sep=';')

colonne = ['Genere', 'Titolo di studio', 'Qualifica']

for col in colonne:
    plt.clf()
    df[col].value_counts().plot(kind='pie', autopct='%1.1f%%', startangle=140)
    plt.ylabel('')
    filename_base = col.replace(" ", "_")
    plt.title(col)
    plt.savefig(f'{filename_base}.png', dpi=300, bbox_inches='tight')
    plt.savefig(f'{filename_base}.pdf', format='pdf', bbox_inches='tight')
    plt.savefig(f'{filename_base}.svg', format='svg', bbox_inches='tight')