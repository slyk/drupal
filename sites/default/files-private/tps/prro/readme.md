to add checkbox service

1. stop kasa
1. You should nstall 1.3.12 version from files-private/tps/prro
Create fop2 and fop3 folder in files-private/tps/prro add permissions
copy agent.db and .secret.key and config.json to new location fop2
2. copy @.service file to systems/system
3. change path to valid
4. allow write access to logs /var/log/checkbox/... for toopro user
5. copy agent db and config of fop2 to needed dir
6. sudo systemctl daemon-reload 
7. try run it
watch logs sudo journalctl -u tps-retail@fop2.service -f
add to autorun systemctl enable tps-checkbox@fop2.service
8. add fop3 config
replace logs file in config to 
/var/log/checkbox/rro_agent2.log
replase port numbers (fop2 = 9202, 13002)
