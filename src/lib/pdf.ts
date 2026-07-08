let html2canvas: any = null;
let jsPDF: any = null;

export async function downloadPdf(elementId: string, filename: string) {
  if (!html2canvas) html2canvas = (await import('html2canvas')).default;
  if (!jsPDF) jsPDF = (await import('jspdf')).jsPDF;

  const element = document.getElementById(elementId);
  if (!element) return;

  const canvas = await html2canvas(element, {
    scale: 2,
    useCORS: true,
    logging: false,
    backgroundColor: '#ffffff',
  });

  const imgData = canvas.toDataURL('image/png');
  const pdf = new jsPDF('p', 'mm', 'a4');
  const pdfWidth = pdf.internal.pageSize.getWidth();
  const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

  let remainingHeight = pdfHeight;
  let srcY = 0;
  const pageHeight = pdf.internal.pageSize.getHeight();

  if (pdfHeight <= pageHeight) {
    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
  } else {
    while (remainingHeight > 0) {
      const srcSliceHeight = Math.min(canvas.height * (pageHeight / pdfHeight), canvas.height - srcY);
      const canvasSlice = document.createElement('canvas');
      canvasSlice.width = canvas.width;
      canvasSlice.height = srcSliceHeight;
      const ctx = canvasSlice.getContext('2d')!;
      ctx.drawImage(canvas, 0, srcY, canvas.width, srcSliceHeight, 0, 0, canvas.width, srcSliceHeight);

      const sliceImgData = canvasSlice.toDataURL('image/png');
      const sliceHeightOnPage = (srcSliceHeight / canvas.height) * pdfHeight;
      pdf.addImage(sliceImgData, 'PNG', 0, 0, pdfWidth, sliceHeightOnPage);

      srcY += srcSliceHeight;
      remainingHeight -= sliceHeightOnPage;
      if (remainingHeight > 0) pdf.addPage();
    }
  }

  pdf.save(filename);
}
