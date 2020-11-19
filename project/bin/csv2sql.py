# -*- coding: iso-8859-1 -*
import sys, pandas as pd
from sqlalchemy import create_engine
engine = create_engine('sqlite:///'+sys.argv[1], echo=False, encoding='iso-8859-1')

sys.stderr.write(sys.argv[2]+"/export_bi_multicontrats.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_multicontrats.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={})
csv.to_sql('multicontrats', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_dr.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_dr.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={})
csv.to_sql('dr', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_ds.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_ds.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={})
csv.to_sql('ds', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_drm.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_drm.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={"#DRM ID": "DRM ID", 'numéro archivage': 'numero archivage'})
csv.to_sql('drm', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_mouvements.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_mouvements.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={'pays export (si export)': 'pays export', '#MOUVEMENT': "type de document"})
csv.to_sql('mouvement', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_etablissements.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_etablissements.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={ 'statut (ACTIF, SUSPENDU)': 'statut', "#ETABLISSEMENT": "type de document"})
csv.to_sql('etablissement', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_societes.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_societes.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={ 'statut (ACTIF, SUSPENDU)': 'statut', "#SOCIETE": "type de document"})
csv.to_sql('societe', con=engine, if_exists='replace')

sys.stderr.write(sys.argv[2]+"/export_bi_drm_stock.csv\n")
csv = pd.read_csv(sys.argv[2]+"/export_bi_drm_stock.csv", encoding='iso-8859-1', delimiter=";", index_col=False).rename(columns={"#ID": "id stock"})
csv.to_sql('DRM_Stock', con=engine, if_exists='replace')
