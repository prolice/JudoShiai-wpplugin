# What is JudoSignUp
JudoSignUp is an easy to use, flexible, mobile friendly competitor registration website for Judo tournaments. You can host it on any webhosting platform with php support, even free hosters are available. Competitors can register for your tournamen. The entered registration data is stored in a CSV file that can be easily imported by [JudoShiai](http://www.judoshiai.fi/index-en.php). Tournament Information as well as the configured weight and age categories for the Tournament can be read form the JudoShiai file format.
# How to Install
Download the JudoSignUp.zip file from the [releases](https://github.com/Xilaew/JudoShiai-xPack/releases) page and unpack it on your php enabled webspace. Next you should edit the config.php file in order to tailer the website to your needs. All available options are well documented by the comments. Then you should prepare a JudoShiai Tournament file with JudoShiai, where you configure the Name, Date and Place of the Tournament, as well as the age and weight categories the competitors shall be registered for.
Now your registration website is up and running, you can include the URL into your annoncement. After registration closing you can download all competitor dats in CSV format from `[url of your registration page]/data.csv`
# List of Free Hosters
On the following free webhosting platforms the JudoSignUp webpage is known to work correctly:
* [https://www.bplaced.net/](https://www.bplaced.net/)
