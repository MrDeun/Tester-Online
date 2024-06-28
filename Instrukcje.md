# Tester Online

Wymagania sprzętowe:
	
	- Komputerem z system Windows 10/11
	- Współczesna przeglądarka internetowa  
	- XAAMP w wersji 8.2.12
	
Instalacja:
	
	1. Po pobraniu repozytorium, nalezy przeniesc folder "Tester Online" 
	   do folderu "htdocs" w instalacji XAAMP'a
	2. Włączyć XAAMP'a i włączyć usługi "Apache" i "MySQL".
	3. W przeglądarcę należy wejść na adres 
	   'http://localhost/phpmyadmin/' 
	   i stworzyć nową bazę danych o nazwie 'tester'
	4. Po utworzeniu bazy, nalezy zaimportowac tester.sql 
	   w folderze "Tester Online" do bazy 
	   poprzez zakładkę 'import' na stronie phpmyadmin.
	5. Po poprawnym zaimportowaniu pliku SQL, nalezy wlaczyc 
	   'events schedule status' w bazie "tester"
	