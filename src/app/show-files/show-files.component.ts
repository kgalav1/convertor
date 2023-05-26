import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { saveAs } from 'file-saver';
import * as JSZip from 'jszip';
// import htmlToImage from 'html-to-image';
import { toCanvas } from 'html-to-image';

@Component({
  selector: 'app-show-files',
  templateUrl: './show-files.component.html',
  styleUrls: ['./show-files.component.css']
})

//this file to use image-converter
export class ShowFilesComponent implements OnInit {

  constructor() { } 

  // Input property to receive selected files from parent component
  @Input() selectedFiles: any;
  @Input() extension: any;
  @Input() data: any;

  // Output property to emit selected page event to parent component
  @Output() SelectedPage: EventEmitter<any> = new EventEmitter<any>();

  ngOnInit(): void {
    // Initialization logic goes here
    
  } 

  // Function to get the file extension from a File object
  getFileExtension(file: File): string {
    const extension = file.name.split('.').pop();
    return extension.toUpperCase();
  }

  // Function to get the file extension from a file name string
  private getFileExtensionByName(fileName: string): string {
    return fileName.split('.').pop() || '';
  }

  // Function to get the display name of a file, truncating if necessary
  getFileDisplayName(fileName: string): string {
    const maxLength = 10;
    if (fileName.length > maxLength) {
      const fileExtension = this.getFileExtensionByName(fileName);
      const fileNameWithoutExtension = fileName.slice(0, maxLength - fileExtension.length);
      return fileNameWithoutExtension + '...' + fileExtension;
    }
    return fileName;
  }

  // Function to download a file
  downloadFile(file: File) {
    const url = URL.createObjectURL(file);
    const a = document.createElement('a');
    a.href = url;
    a.download = file.name;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  // convertFile(file: File) {
  //   if (!confirm("Do you want to convert this file?")) {
  //     return;
  //   }

  //   const inputFileExtension = this.getFileExtension(file);
  //   const targetExtension = 'jpg';

  //   // Perform conversion only if the input file extension is different from the target extension
  //   if (inputFileExtension.toUpperCase() !== targetExtension.toUpperCase()) {
  //     const img = new Image();
  //     const reader = new FileReader();

  //     reader.onload = () => {
  //       img.onload = async () => {
  //         const canvas = document.createElement('canvas');
  //         const ctx = canvas.getContext('2d');
  //         canvas.width = img.width;
  //         canvas.height = img.height;
  //         ctx.drawImage(img, 0, 0);

  //         // Convert canvas to target format using html-to-image library
  //         // const dataUrl = await toCanvas(canvas, { mimeType: `image/${targetExtension}` });

  //         // Create a Blob from the converted data URL
  //         // const blob = this.dataURLToBlob(dataUrl.toDataURL(`image/${targetExtension}`));

  //         // Create a File object with the target extension and download it
  //         // const convertedFile = new File([blob], `${file.name}.${targetExtension}`, { type: `image/${targetExtension}` });
  //         // this.downloadFile(convertedFile);
  //       };
  //       img.src = reader.result as string;
  //     };

  //     reader.onerror = () => {
  //       console.error('Error reading file.');
  //     };

  //     reader.readAsDataURL(file);
  //   } else {
  //     console.log('File is already in the target format. Conversion not required.');
  //   }
  // }

  // dataURLToBlob(dataUrl: string): Blob {
  //   const arr = dataUrl.split(',');
  //   const mime = arr[0].match(/:(.*?);/)?.[1];
  //   const bstr = atob(arr[1]);
  //   let n = bstr.length;
  //   const u8arr = new Uint8Array(n);

  //   while (n--) {
  //     u8arr[n] = bstr.charCodeAt(n);
  //   }

  //   return new Blob([u8arr], { type: mime });
  // }

  // Function to delete a file from the selected files array
  deleteFile(index: number) {
    this.selectedFiles.splice(index, 1);
  }
  createZip() {
    if (!this.selectedFiles || this.selectedFiles.length === 0) {
      console.log('No files selected.');
      return;
    }

    const zip = new JSZip();

    // Add each file to the ZIP
    for (let i = 0; i < this.selectedFiles.length; i++) {
      const file = this.selectedFiles[i];
      zip.file(file.name, file);
    }

    // Generate the ZIP file asynchronously
    zip.generateAsync({ type: 'blob' })
      .then(function (content) {
        // Save the ZIP file
        saveAs(content, 'example.zip')  ;
      });
  }


  

  // async uploadAndConvert(file: File) {
  //   try {
  //     const inputFileBuffer = await this.readFile(file);

  //     // Use Sharp to convert the image
  //     const convertedImageBuffer = await sharp(inputFileBuffer)
  //       .resize(800, 600) // Example: Resize the image to 800x600 pixels
  //       .toFormat('jpeg') // Example: Convert the image to JPEG format
  //       .toBuffer();

  //     // Create a new file name for the converted image
  //     const outputFileName = this.generateOutputFileName(file.name);
  //     const outputFilePath = `path/to/output/${outputFileName}`;

  //     // Write the converted image buffer to the output file
  //     await this.writeFile(outputFilePath, convertedImageBuffer);

  //     // Generate the download link for the converted file
  //     const downloadUrl = URL.createObjectURL(new Blob([convertedImageBuffer]));
  //     this.downloadLink = downloadUrl;

  //     console.log('File uploaded and converted successfully!');
  //   } catch (error) {
  //     console.error('Error occurred during file upload and conversion:', error);
  //   }
  // }

  // readFile(file: File): Promise<ArrayBuffer> {
  //   return new Promise<ArrayBuffer>((resolve, reject) => {
  //     const reader = new FileReader();
  //     reader.onload = () => resolve(reader.result as ArrayBuffer);
  //     reader.onerror = reject;
  //     reader.readAsArrayBuffer(file);
  //   });
  // }

  // writeFile(path: string, buffer: Buffer): Promise<void> {
  //   return new Promise<void>((resolve, reject) => {
  //     fs.writeFile(path, buffer, (error) => {
  //       if (error) {
  //         reject(error);
  //       } else {
  //         resolve();
  //       }
  //     });
  //   });
  // }

//   convertImage(file: File) {
//     const reader = new FileReader();
//     // Check if the file has the same extension as the desired file extension
//     const fileExtensionRegex = /(?:\.([^.]+))?$/;
    
//     const originalExtension = fileExtensionRegex.exec(file.name)[1];
// console.log(originalExtension)
//     if ('.'+originalExtension === this.extension) {
//       console.log('Selected file has a different extension. Please choose a file with the correct extension.');
//       alert('Selected file has a different extension. Please choose a file with the correct extension.')
//       return false;
//     }

//     reader.onload = (event: any) => {
//       const img = new Image();

//       img.onload = () => {
//         const canvas = document.createElement('canvas');
//         const ctx = canvas.getContext('2d');

//         // Resize the image
//         const maxWidth = 1200;
//         const maxHeight =800;
//         let width = img.width;
//         let height = img.height;

//         if (width > maxWidth) {
//           height *= maxWidth / width;
//           width = maxWidth;
//         }

//         if (height > maxHeight) {
//           width *= maxHeight / height;
//           height = maxHeight;
//         }

//         canvas.width = width;
//         canvas.height = height;

//         // Draw the resized image on the canvas
//         ctx.drawImage(img, 0, 0, width, height);

//         // Convert the canvas image to a data URL with the specified file extension
//         const convertedImageDataUrl = canvas.toDataURL(`image/${this.extension}`);

//         // Create a download link for the converted image
//         const downloadLink = document.createElement('a');
//         downloadLink.href = convertedImageDataUrl;
//         downloadLink.download = `converted_image.${this.extension}`;

//         // Append the download link to the DOM
//         document.body.appendChild(downloadLink);

//         // Trigger the download
//         downloadLink.click();

//         // Clean up the download link from the DOM
//         document.body.removeChild(downloadLink);
//       };

//       img.src = event.target.result;
//     };

//     reader.readAsDataURL(file);
//   }

  convertImage(file: File) {
     const width : any = this.data.imageWidth;
     const height : any =this.data.imageHeight;
    const reader = new FileReader();

    reader.onload = (event: any) => {
      const img = new Image();

      img.onload = () => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Calculate the aspect ratio of the original image
        const aspectRatio = img.width / img.height;

        // Calculate the new dimensions based on the desired width and height while maintaining the aspect ratio
        let newWidth;
        let newHeight;

        if (width / height > aspectRatio) {
          newHeight = height;
          newWidth = height * aspectRatio;
        } else {
          newWidth = width;
          newHeight = width / aspectRatio;
        }

        canvas.width = newWidth;
        canvas.height = newHeight;

        // Draw the resized image on the canvas
        ctx.drawImage(img, 0, 0, newWidth, newHeight);

        // Convert the canvas image to a data URL with the specified file extension
        const convertedImageDataUrl = canvas.toDataURL(`image/${this.extension}`);

        // Create a download link for the converted image
        const downloadLink = document.createElement('a');
        downloadLink.href = convertedImageDataUrl;
        downloadLink.download = `converted_image.${this.extension}`;

        // Append the download link to the DOM
        document.body.appendChild(downloadLink);

        // Trigger the download
        downloadLink.click();

        // Clean up the download link from the DOM
        document.body.removeChild(downloadLink);
      };

      img.src = event.target.result;
    };

    reader.readAsDataURL(file);
  }
}