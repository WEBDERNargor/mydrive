
    document.addEventListener('DOMContentLoaded', function() {
        // สร้าง <link> element
        let link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '/system/css/system'; // เปลี่ยนให้เป็นเส้นทางไปยังไฟล์ CSS ของคุณ
    
        // เพิ่ม <link> element เข้าไปใน <head> ที่ตำแหน่งแรกสุด
        document.head.insertBefore(link, document.head.firstChild);
       
     
        let dev_container = document.createElement('div');
        dev_container.id = 'dev_container';
        dev_container.className = 'dev_container';
        dev_container.innerHTML = `
        <a href="/system/devtools" target="_new" id="dev_button">Developer</a>
        `;
        document.body.insertBefore(dev_container, document.body.firstChild);
        // setTimeout(function() {
        //     dev_button.addEventListener('click', function() {
        //       window.location.href = '/system/devtools';
        //     });
        // }, 1000);
       
    });

