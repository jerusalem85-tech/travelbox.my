import { Controller, Get, Post, Body, Param, Query } from '@nestjs/common';
import { AccountingService } from './accounting.service';
import { CreateJournalEntryDto } from './dto/create-journal-entry.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('accounting')
export class AccountingController {
  constructor(private accounting: AccountingService) {}

  @Get('accounts')
  getChartOfAccounts() {
    return this.accounting.getChartOfAccounts();
  }

  @Post('accounts')
  createAccount(@Body() data: { code: string; name: string; category: any; parentId?: string }) {
    return this.accounting.createAccount(data);
  }

  @Get('journal')
  getJournalEntries(
    @Query('tripId') tripId?: string,
    @Query('accountId') accountId?: string,
    @Query('from') from?: string,
    @Query('to') to?: string,
  ) {
    return this.accounting.getJournalEntries(tripId, accountId, from, to);
  }

  @Post('journal')
  createManualEntry(
    @Body() dto: CreateJournalEntryDto,
    @CurrentUser('id') userId: string,
  ) {
    return this.accounting.createManualEntry(dto, userId);
  }

  @Get('trial-balance')
  getTrialBalance() {
    return this.accounting.getTrialBalance();
  }

  @Get('profit-loss')
  getProfitAndLoss(@Query('from') from?: string, @Query('to') to?: string) {
    return this.accounting.getProfitAndLoss(from, to);
  }

  @Get('trip/:tripId/pnl')
  getTripProfitAndLoss(@Param('tripId') tripId: string) {
    return this.accounting.getTripProfitAndLoss(tripId);
  }
}
