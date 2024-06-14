import requests
from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from bs4 import BeautifulSoup
import csv

'''form_test.py permet de tester des injections sql et attaques XSS'''

def find_form(url):
    """Fonction permettant d'enregistrer le formulaire en executant dans un navigateur"""
    driver = webdriver.Chrome() #Ouvre navigateur Chrome (pour pourvoir executer les .js)
    driver.get(url) #Permet d'ouvrir la page de connexion
    wait = WebDriverWait(driver, 10) # Attendre que les éléments du formulaire soient présents
    html_out = driver.page_source # Extraire le contenu de la page html retour
    soup = BeautifulSoup(html_out, 'html.parser')# Analyser le contenu HTML
    form = soup.find('form') # Extraire les élement entre les balises form
    return str(form)

def find_user_pswd_button(url, input_name = 'data-rel', type_login = 'text', type_password = 'password', type_button = 'button'):
    """Trouve le nom des entrées du formulaire et l'id du boutton"""
    name_data = input_name #Nom du type de variable pour le login et password
    form = find_form(url)
    soup = BeautifulSoup(form, 'html.parser')
    form = soup.find('form')
    user = form.find('input', {'type': type_login}) #Cherche les entrées de type text
    user_name = user[name_data]
    pswd = form.find('input', {'type': type_password}) #Cherche les entrées de type password
    pswd_name = pswd[name_data]
    try:
        button = form.find('button', {'type' : type_button}) #Cherche le boutton d'envoie du formulaire
        button_id = button['id']
    except:
        button_id = ''
        print("Error to find button")
    return str(user_name), str(pswd_name), str(button_id)

'''======Données spécifique formulaire======'''
url = 'http://localhost:5173/' #URL du formulaire
url_back = 'http://localhost:8003/signin' #URL du back
error_message = "No user were found with this credentials, using password" #Message de réponse d'erreur du formulaire

"""Liste d'exploits"""
exploit_name = ["Correct login/password", "False login/password", 
                "SQL Injection Login 1", "SQL Injection Login 2",
                "SQL Injection Password 1", "SQL Injection Password 2",
                "XSS 1", "XSS 2"]
login_list = ["admin", "admin" ,
                "' OR '1'='1'; --", "or 1-- -' or 1 or '1'or 1 or'",
                "false_login", "false_login",
                "<script>alert(document.domain)</script>", "<img src/onerror=alert(document.cookie)>"]
password_list = ["super_admin", "false_password",
                  "false_password", "false_paswd",
                  "' OR '1'='1'; --", "or 1-- -' or 1 or '1'or 1 or'",
                  "<script>alert(document.domain)</script>", "<img src/onerror=alert(document.cookie)>"]
'''======================================'''

#Récupère le formulaire
form = find_form(url)
print(form)

#Initialisation des payloads
user_name, pswd_name, button_id = find_user_pswd_button(url)

#Incrémentation du dictionnaire des exploits à tester
payloads={}
for i in range (len(login_list)):
    payloads[i] = {user_name : login_list[i],
                pswd_name : password_list[i],
                }

#Ouverture du fichier d'écriture
fileName="Exploits_rapport.csv" #+str(dt.datetime.now().hour)+str(dt.datetime.now().minute)+str(dt.datetime.now().second)
file = open("./"+fileName, "w") 
writer = csv.writer(file, delimiter=';')
writer.writerow(["EXPLOIT NAME","RESULT", "LOGIN", "PASSWORD"])

#Boucle test d'exploits
for i in range (len(exploit_name)):
    # Envoyer la requête POST
    response = requests.post(url_back, data=payloads[i])
    # Récupération de la réponse
    response_text = response.text
    print(response_text)
    # Test sur la réponse
    if response.status_code == 200:
        if "XSS" in exploit_name[i]: #Condition sur attaque xss
            print(f"Requete bien effectué pour : {exploit_name[i]}")
            if "<" in response_text:
                if ">" in response_text:
                    resultat = "success XSS attack"
                    print(resultat+ "\n")
            else:
                resultat = "failed XSS attack"
                print(resultat+ "\n")
                print(login_list[i])
        else:
            if error_message in response_text:
                resultat = "Access Denied"
                print(resultat+ "\n")
            else:
                resultat = "Access accepted"
                print(resultat + "\n")
    else:
        resultat = f"Problème d'envoie requête à {url_back} pour tester {exploit_name[i]}"
        print(resultat +"\n")
    writer.writerow([exploit_name[i],resultat, login_list[i], password_list[i]]) #Ecriture fichier csv
file.close()



