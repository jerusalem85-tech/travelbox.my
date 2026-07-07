import { IsString, IsOptional, IsNumber, IsDateString } from 'class-validator';

export class CreateExpenseDto {
  @IsString()
  description: string;

  @IsString()
  category: string;

  @IsOptional()
  @IsNumber()
  amount?: number;

  @IsOptional()
  @IsDateString()
  date?: string;

  @IsOptional()
  @IsString()
  notes?: string;
}
