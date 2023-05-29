import { Component, Input, ViewChild, OnInit, ElementRef } from '@angular/core';
import * as JSZip from 'jszip';

@Component({
  selector: 'app-unzipfile',
  templateUrl: './unzipfile.component.html',
  styleUrls: ['./unzipfile.component.css']
})
export class UnzipfileComponent implements OnInit {

  constructor() { }
  @ViewChild('fileInput') fileInput: ElementRef;
  selectedFiles: File[] = [];
  extension: string = '';
  extractedFiles: { fileName: string, filePath: string }[] = [];
  data: object = {
    "files": this.selectedFiles,
    'extension': this.extension
  }
  ngOnInit(): void {
  }

  onFileSelected(event: any) {
    const fileInput = event.target;
    const fileList = fileInput.files;

    // Check if a file was selected
    if (fileList.length === 0) {
      alert('Please select a file.');
      return;
    }

    const zipFile = fileList[0];
    const zip = new JSZip();

    zip.loadAsync(zipFile)
      .then((zipData: JSZip) => {
        // Clear the extracted files array
        this.extractedFiles = [];

        // Extract the files to a temporary directory
        const tempDir = 'temp/';
        zip.forEach((relativePath, zipEntry) => {
          if (!zipEntry.dir) {
            const fileName = zipEntry.name;
            const filePath = tempDir + fileName;

            zipEntry.async('blob').then((fileData: Blob) => {
              const fileURL = URL.createObjectURL(fileData);
              this.extractedFiles.push({ fileName, filePath: fileURL });
            });
          }
        });
      })
      .catch((error: any) => {
        console.error('Error extracting the zip file:', error);
      });
  }

  downloadFile(filePath: string) {
    const link = document.createElement('a');
    link.href = filePath;
    link.download = filePath.split('/').pop() || 'file';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  deleteFile(filePath: string) {
    // Implement your logic to delete the file here
    console.log('Deleted file:', filePath);
  }

  onFileDrop(event: DragEvent) {
    event.preventDefault();
    const files: FileList = event.dataTransfer.files;
    for (let i = 0; i < files.length; i++) {
      this.selectedFiles.push(files[i]);
    }
    // console.log(this.selectedFiles); // Do something with the dropped files
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