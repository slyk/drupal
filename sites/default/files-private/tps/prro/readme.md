to add checkbox service

Актуальну версію каси ви можете завантажити за посиланням

https://api.checkbox.ua/update-service/api/v1/rro_agent/versions/latest/linux-x86_64/installer


1. stop kasa
2. You should nstall 1.3.12 version from files-private/tps/prro
Create fop2 and fop3 folder in files-private/tps/prro add permissions
copy agent.db and .secret.key and config.json to new location fop2
3. copy @.service file to systems/system
4. change path to valid
5. allow write access to logs /var/log/checkbox/... for toopro user
6. sudo mkdir /var/log/checkbox 
7. sudo chown toopro:www-data /var/log/checkbox -R
8. sudo chmod og+rw /var/log/checkbox -R
9. copy agent db and config of fop2 to needed dir
10. sudo systemctl daemon-reload 
11. try run it
watch logs sudo journalctl -u tps-retail@fop2.service -f
add to autorun systemctl enable tps-checkbox@fop2.service
12. add fop3 config
replace logs file in config to 
/var/log/checkbox/rro_agent2.log
replase port numbers (fop2 = 9202, 13002)

### update 05.2025 for QRs
```bash
# check that you CLOSED shift before update!!!
wget https://nfeya.com/tps/checkbox-rro-agent-v1.6.7.deb
sudo service tps-checkbox@fop2 stop
sudo service tps-checkbox@fop3 stop
sudo dpkg -i checkbox-rro-agent-v1.6.7.deb
#sudo systemctl daemon-reload
sudo service tps-checkbox@fop2 start
```
