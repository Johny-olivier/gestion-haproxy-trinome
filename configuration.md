### MODIFIER 

backup : sudo cp /etc/haproxy/haproxy.cfg /etc/haproxy/haproxy.cfg.bak 

sudo nano /etc/haproxy/haproxy.cfg

ajouter  avant defaults 
    stats socket /run/haproxy/admin.sock user haproxy group www-data mode 660 level admin

installer : socat 
sudo apt install socat -y