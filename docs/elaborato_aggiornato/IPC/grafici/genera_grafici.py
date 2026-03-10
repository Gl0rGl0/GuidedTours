import pandas as pd
import matplotlib.pyplot as plt

df = pd.read_csv('../profili_utente.csv', sep=';')

colonne = ['Genere', 'Titolo di studio', 'Qualifica']

for col in colonne:
    plt.clf()
    df[col].value_counts().plot(kind='pie', autopct='%1.1f%%', startangle=140)
    plt.ylabel('')
    plt.title(col)
    plt.savefig(f'{col.replace(" ", "_")}.png', bbox_inches='tight')