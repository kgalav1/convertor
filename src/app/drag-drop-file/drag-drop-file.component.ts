import { Component, OnInit, Input, Output, ViewChild, ElementRef, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-drag-drop-file',
  templateUrl: './drag-drop-file.component.html',
  styleUrls: ['./drag-drop-file.component.css']
})
export class DragDropFileComponent implements OnInit {
  selectedFiles: File[] = [];
  extentions :string;

  constructor() { }

  @ViewChild('fileInput') fileInput: ElementRef;
  @Output() SelectedFile: EventEmitter<any> = new EventEmitter<any>();
  @Input() fileName: string;
  @Input() fileType: string;
  @Input() allowedExtensions: [];
  @Input() allowedExtensions_Error: string;
 
  ngOnInit(): void {
    this.extentions= this.allowedExtensions.map(ext => `.${ext}`).join(',');
  }

  selectedFile(data: any) {
    this.SelectedFile.emit(data);
  }

  onFileSelected() {
    const files: FileList = this.fileInput.nativeElement.files;
    for (let i = 0; i < files.length; i++) {
      

      const reader = new FileReader();
      // Check if the file has the same extension as the desired file extension
      const fileExtensionRegex = /(?:\.([^.]+))?$/;

      const originalExtension = fileExtensionRegex.exec(files[i].name)[1];
      
      const allowedExtensions : string[]= this.allowedExtensions;// Add more extensions if needed
      if (!allowedExtensions.includes(originalExtension.toLowerCase())) {
        console.log(this.allowedExtensions_Error);
        alert(this.allowedExtensions_Error);
        return false
      }
      
      this.selectedFiles.push(files[i]);


    }
    this.selectedFile(this.selectedFiles)
  }

  // getFileExtension(file: File): string {
  //   const extension = file.name.split('.').pop();
  //   return extension.toUpperCase();
  // }

  // onExtensionSelected(extension: string) {
  //   this.extension = extension;
  //   // Perform any additional actions based on the selected extension
  // }

  onFileDrop(event: DragEvent) {
    event.preventDefault();
    const files: FileList = event.dataTransfer.files;
    for (let i = 0; i < files.length; i++) {
      this.selectedFiles.push(files[i]);
    }
    this.selectedFile(this.selectedFiles)
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
}