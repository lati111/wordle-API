import './bootstrap';



function messageDeleter() {
    setTimeout(() => {
        document.getElementsByClassName("error").array.forEach(element => {
            element.remove();
        });
        document.getElementsByClassName("message").array.forEach(element => {
            element.remove();
        });
    }, 5000);

}

messageDeleter()
