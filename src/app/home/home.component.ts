import { Component, OnInit ,Input,ViewChild ,ElementRef} from '@angular/core';


@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})

export class HomeComponent implements OnInit {
    public isCollapsed = true;
    @ViewChild('fileInput') fileInput: ElementRef;
    model = {
        left: true,
        middle: false,
        right: false
    };
    selectedFiles: File[] = [];
    extension : string ='';
    imageWidth : number = 1200; 
    imageHeight : number =800;

    data: object = { "files" : this.selectedFiles,
     'extension' : this.extension,
      'width' : this.imageWidth ,
      'height' : this.imageHeight
    }

    @Input() fileName: string;
  @Input() fileType: string;

  onFileSelected() {
    const files: FileList = this.fileInput.nativeElement.files;
    for (let i = 0; i < files.length; i++) {
      this.selectedFiles.push(files[i]);
    }
    this.data = { "files" : this.selectedFiles,
    'extension' : this.extension,
     'width' : this.imageWidth ,
     'height' : this.imageHeight
   };
    // console.log(this.selectedFiles); // Do something with the selected files
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
  
  onFileDrop(event: DragEvent) {
    event.preventDefault();
    const files: FileList = event.dataTransfer.files;
    for (let i = 0; i < files.length; i++) {
      this.selectedFiles.push(files[i]);
    }
    console.log(this.selectedFiles); // Do something with the dropped files
  }

  onDragOver(event: DragEvent) {
    event.preventDefault();
    event.stopPropagation();
    event.dataTransfer.dropEffect = 'copy';
    const dropZone = event.target as HTMLElement;
    dropZone.classList.add('drag-over');
  }

  onDragLeave(event: DragEvent) {
    event.stopPropagation();
    const dropZone = event.target as HTMLElement;
    dropZone.classList.remove('drag-over');
  }
    focus;
    focus1;
    constructor() { }

    ngOnInit() {}
    
}
