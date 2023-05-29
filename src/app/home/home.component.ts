import { Component, OnInit ,Input,ViewChild ,ElementRef} from '@angular/core';


@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})

export class HomeComponent implements OnInit {
    public isCollapsed = true;

    @ViewChild('fileInput') fileInput: ElementRef;
    @Input() fileName: string;
    @Input() fileType: string;



    model = {
        left: true,
        middle: false,
        right: false
    };
    selectedFiles: File[] = [];
    extension : string ='';
    imageWidth : number = 1200; 
    imageHeight : number =800;
    allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'tiff'];
    allowedExtensions_Error ="Selected file is not a supported image. Please choose an image file.";
    data: object = { "files" : this.selectedFiles,
     'extension' : this.extension,
      'width' : this.imageWidth ,
      'height' : this.imageHeight
    }


  

  onselectedFile(files: File[]) {
    // Handle the selected file(s) received from the child component
    this.selectedFiles = files;

    this.data = { "files" : this.selectedFiles,
    'extension' : this.extension,
     'width' : this.imageWidth ,
     'height' : this.imageHeight
   };
    // Perform further actions with the file(s)
  }
 
 
  getFileExtension(file: File): string {
    const extension = file.name.split('.').pop();
    return extension.toUpperCase();
  }
  onExtensionSelected(extension: string) {
    this.extension = extension;
    console.log('Selected File Extension:', extension);
    // Perform any additional actions based on the selected extension
  }
  
 
    focus;
    focus1;
    constructor() { }

    ngOnInit() {}
    
}
