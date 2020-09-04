# -*- coding: iso-8859-1 -*
import sys, pandas as pd
from sqlalchemy import create_engine
engine = create_engine('sqlite:///'+sys.argv[1], echo=False, encoding='iso-8859-1')

sys.stderr.write("data/mercuriales/datas_mercuriale.csv\n")
csv = pd.read_csv("data/mercuriales/datas_mercuriale.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={})
csv.to_sql('contrats', con=engine, if_exists='replace')

