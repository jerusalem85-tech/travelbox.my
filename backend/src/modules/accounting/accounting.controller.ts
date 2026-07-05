import { Controller, Get, Post, Put, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { AccountingService } from './accounting.service';

@ApiTags('Accounting')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('accounting')
export class AccountingController {
  constructor(private service: AccountingService) {}

  @Post('invoices') createInvoice(@Body() data: any) { return this.service.createInvoice(data); }
  @Get('invoices') findAllInvoices(@Query() query: any) { return this.service.findAllInvoices(query); }
  @Get('invoices/:id') findInvoiceById(@Param('id') id: string) { return this.service.findInvoiceById(id); }
  @Put('invoices/:id') updateInvoice(@Param('id') id: string, @Body() data: any) { return this.service.updateInvoice(id, data); }
  @Post('credit-notes') createCreditNote(@Body() data: any) { return this.service.createCreditNote(data); }
  @Post('expenses') createExpense(@Body() data: any) { return this.service.createExpense(data); }
  @Get('expenses') findAllExpenses(@Query() query: any) { return this.service.findAllExpenses(query); }
  @Get('trial-balance') getTrialBalance(@Query('startDate') startDate?: string, @Query('endDate') endDate?: string) { return this.service.getTrialBalance(startDate, endDate); }
}
