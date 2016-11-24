#Installation de l'application CIVA

##Renseigner la ou les machines

Creer un fichier ``inventory/hosts`` qui permet à ansible de retrouver l'ip de la ou les machines :

     [serveurs]
     192.168.1.1
     192.168.1.2

##Executer la recette de deploiement

     ansible-playbook -i inventory deploy.yml
