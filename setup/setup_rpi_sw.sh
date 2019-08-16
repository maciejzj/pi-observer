setup_webserver()
{
	rm -r /var/www/html
	cp -r html /var/www/
	chown -R pi:www-data /var/www/html/
	chmod -R 770 /var/www/html/
}

