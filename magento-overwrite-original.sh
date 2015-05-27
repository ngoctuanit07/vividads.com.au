find . -type f -exec chattr -a /home/tablethr/www/{} \;
find . -type f -exec chattr -i /home/tablethr/www/{} \;
# copy fies
find . -type f -exec cp {} /home/tablethr/www/{} \;
find . -type f -exec chattr +i /home/tablethr/www/{} \;

